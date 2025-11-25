<?php
// Simple autoloader for PHPMailer classes
spl_autoload_register(function ($class) {
    // PHPMailer classes
    $phpmailer_classes = [
        'PHPMailer\\PHPMailer\\PHPMailer' => 'PHPMailer/src/PHPMailer.php',
        'PHPMailer\\PHPMailer\\Exception' => 'PHPMailer/src/Exception.php',
        'PHPMailer\\PHPMailer\\SMTP' => 'PHPMailer/src/SMTP.php',
        'PHPMailer\\PHPMailer\\POP3' => 'PHPMailer/src/POP3.php'
    ];
    
    if (isset($phpmailer_classes[$class])) {
        require_once __DIR__ . '/../' . $phpmailer_classes[$class];
        return true;
    }
    
    // Application classes
    if (file_exists(__DIR__ . '/' . $class . '.php')) {
        require_once __DIR__ . '/' . $class . '.php';
        return true;
    }
    
    return false;
});

// Include required files manually
require_once __DIR__ . '/config.php';
?>