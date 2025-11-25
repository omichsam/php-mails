<?php
// Start session
session_start();

// Include configuration
require_once 'includes/config.php';

// CSRF Token Generation
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Display flash messages
function displayFlashMessage() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        $alertClass = $flash['type'] === 'success' ? 'alert-success' : 'alert-danger';
        echo '<div class="alert ' . $alertClass . ' alert-dismissible fade show" role="alert">';
        echo htmlspecialchars($flash['message']);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
        unset($_SESSION['flash']);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Mailer - Send Email</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 20px;
        }
        .container {
            max-width: 700px;
        }
        .email-form {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .form-label {
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="email-form">
                    <h1 class="text-center mb-4">📧 PHP Mailer</h1>
                    <p class="text-center text-muted mb-4">Send emails easily with our PHP mailer</p>
                    
                    <?php displayFlashMessage(); ?>
                    
                    <form method="POST" action="send_email.php" id="emailForm">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="email" class="form-label">Recipient Email *</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       placeholder="recipient@example.com" required>
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label for="subject" class="form-label">Subject *</label>
                                <input type="text" class="form-control" id="subject" name="subject" 
                                       placeholder="Email subject" required>
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label for="message" class="form-label">Message *</label>
                                <textarea class="form-control" id="message" name="message" 
                                          rows="8" placeholder="Type your message here..." required></textarea>
                            </div>
                            
                            <div class="col-md-12 mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_html" name="is_html" checked>
                                    <label class="form-check-label" for="is_html">
                                        Send as HTML email
                                    </label>
                                </div>
                                <small class="text-muted">Uncheck to send as plain text</small>
                            </div>
                            
                            <div class="col-md-12">
                                <button type="submit" name="send" class="btn btn-primary btn-lg w-100">
                                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                    Send Email
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                
                <div class="text-center mt-4 text-muted">
                    <small>Powered by PHPMailer</small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form submission handling
        document.getElementById('emailForm').addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            const spinner = submitBtn.querySelector('.spinner-border');
            
            submitBtn.disabled = true;
            spinner.classList.remove('d-none');
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...';
        });
        
        // Character counter for subject
        const subjectInput = document.getElementById('subject');
        const messageInput = document.getElementById('message');
        
        subjectInput.addEventListener('input', function() {
            const maxLength = 255;
            const currentLength = this.value.length;
            
            if (currentLength > maxLength) {
                this.value = this.value.substring(0, maxLength);
            }
        });
    </script>
</body>
</html>