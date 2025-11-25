<?php
// Include required files
require_once 'includes/autoload.php';

// Start session for flash messages and CSRF protection
session_start();

// Set security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// CSRF Token Generation
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send'])) {
    
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['flash'] = [
            'type' => 'error',
            'message' => 'Security token validation failed. Please try again.'
        ];
        header('Location: index.php');
        exit;
    }
    
    // Validate and sanitize inputs
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);
    $isHtml = isset($_POST['is_html']) ? true : false;
    
    // Basic validation
    $errors = [];
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }
    
    if (empty($subject)) {
        $errors[] = 'Please enter a subject.';
    }
    
    if (empty($message)) {
        $errors[] = 'Please enter a message.';
    }
    
    if (!empty($errors)) {
        $_SESSION['flash'] = [
            'type' => 'error',
            'message' => implode('<br>', $errors)
        ];
        header('Location: index.php');
        exit;
    }
    
    try {
        // Initialize mail sender
        $mailSender = new MailSender();
        
        // Send email
        $result = $mailSender->send($email, $subject, $message, $isHtml);
        
        if ($result) {
            $_SESSION['flash'] = [
                'type' => 'success',
                'message' => 'Email sent successfully!'
            ];
        }
        
    } catch (Exception $e) {
        $_SESSION['flash'] = [
            'type' => 'error',
            'message' => 'Failed to send email: ' . $e->getMessage()
        ];
    }
    
    // Redirect back to form
    header('Location: index.php');
    exit;
}

// If not POST request, redirect to form
header('Location: index.php');
exit;
?>