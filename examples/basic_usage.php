<?php

/**
 * Example: Sending a basic email using PhpMails
 *
 * This example demonstrates how to use the Mail class to send emails
 * with SMTP authentication using PHPMailer.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use PhpMails\Mail;
use PHPMailer\PHPMailer\Exception;

// Create a new Mail instance
$mail = new Mail();

try {
    // Configure SMTP settings
    // Replace these values with your actual SMTP server details
    $mail->configureSMTP(
        host: 'smtp.example.com',      // SMTP server hostname
        port: 587,                      // SMTP port (587 for TLS, 465 for SSL)
        username: 'your-email@example.com',  // Your SMTP username
        password: 'your-password',      // Your SMTP password
        encryption: 'tls'               // Encryption type: 'tls', 'ssl', or ''
    );

    // Set sender and recipients
    $mail->setFrom('sender@example.com', 'Sender Name')
         ->addRecipient('recipient@example.com', 'Recipient Name')
         ->addCC('cc@example.com', 'CC Person')       // Optional: Add CC
         ->addBCC('bcc@example.com', 'BCC Person')    // Optional: Add BCC
         ->addReplyTo('reply@example.com', 'Reply To'); // Optional: Reply-to address

    // Set email content
    $mail->setSubject('Test Email from PhpMails')
         ->setBody('<h1>Hello!</h1><p>This is a <strong>test email</strong> sent using PhpMails.</p>')
         ->setAltBody('Hello! This is a test email sent using PhpMails.'); // Plain text fallback

    // Optional: Add attachments
    // $mail->addAttachment('/path/to/file.pdf', 'document.pdf');

    // Optional: Add embedded images for HTML emails
    // $mail->addEmbeddedImage('/path/to/logo.png', 'logo');
    // Then reference in HTML body: <img src="cid:logo">

    // Optional: Enable debug output for troubleshooting
    // $mail->enableDebug(2);

    // Send the email
    if ($mail->send()) {
        echo "Email sent successfully!\n";
    }
} catch (Exception $e) {
    echo "Email could not be sent. Error: {$mail->getError()}\n";
}
