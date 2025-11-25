<?php
// Email Configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'onwosam@gmail.com');
define('SMTP_PASSWORD', 'your-app-password'); // Use App Password for Gmail
define('SMTP_ENCRYPTION', 'tls'); // 'tls' or 'ssl'

// Email Settings
define('FROM_EMAIL', 'onwosam@gmail.com');
define('FROM_NAME', 'TETSER_MAIL');
define('REPLY_TO_EMAIL', 'onwosam@gmail.com');
define('REPLY_TO_NAME', 'Support Team');

// Application Settings
define('DEBUG_MODE', false);
define('LOG_PATH', __DIR__ . '/../logs/mail.log');
define('MAX_ATTACHMENT_SIZE', 10 * 1024 * 1024); // 10MB

// Security Settings
define('ALLOWED_ATTACHMENT_TYPES', [
    'pdf',
    'doc',
    'docx',
    'txt',
    'jpg',
    'jpeg',
    'png',
    'gif'
]);

// Rate Limiting
define('MAX_EMAILS_PER_HOUR', 50);
?>