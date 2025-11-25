# PHP Mails

A simple PHP library for sending emails using [PHPMailer](https://github.com/PHPMailer/PHPMailer).

## Installation

Install via Composer:

```bash
composer require omichsam/php-mails
```

## Requirements

- PHP 7.4 or higher
- Composer

## Usage

### Basic Email

```php
<?php

require_once 'vendor/autoload.php';

use PhpMails\Mail;

$mail = new Mail();

// Configure SMTP settings
$mail->configureSMTP(
    host: 'smtp.example.com',
    port: 587,
    username: 'your-email@example.com',
    password: 'your-password',
    encryption: 'tls'
);

// Set sender and recipients
$mail->setFrom('sender@example.com', 'Sender Name')
     ->addRecipient('recipient@example.com', 'Recipient Name');

// Set email content
$mail->setSubject('Hello World')
     ->setBody('<h1>Hello!</h1><p>This is a test email.</p>')
     ->setAltBody('Hello! This is a test email.');

// Send the email
try {
    $mail->send();
    echo "Email sent successfully!";
} catch (Exception $e) {
    echo "Error: " . $mail->getError();
}
```

### Available Methods

| Method | Description |
|--------|-------------|
| `configureSMTP($host, $port, $username, $password, $encryption)` | Configure SMTP settings |
| `setFrom($email, $name)` | Set sender email and name |
| `addRecipient($email, $name)` | Add a recipient |
| `addCC($email, $name)` | Add a CC recipient |
| `addBCC($email, $name)` | Add a BCC recipient |
| `addReplyTo($email, $name)` | Add reply-to address |
| `setSubject($subject)` | Set email subject |
| `setBody($body, $isHTML)` | Set email body (HTML by default) |
| `setAltBody($altBody)` | Set plain text alternative body |
| `addAttachment($path, $name)` | Add a file attachment |
| `addEmbeddedImage($path, $cid, $name)` | Add an embedded image |
| `enableDebug($level)` | Enable SMTP debugging |
| `send()` | Send the email |
| `getError()` | Get the last error message |
| `reset()` | Clear all recipients and attachments |
| `getMailer()` | Get the underlying PHPMailer instance |

### Adding Attachments

```php
$mail->addAttachment('/path/to/document.pdf', 'MyDocument.pdf');
```

### Embedded Images

```php
$mail->addEmbeddedImage('/path/to/logo.png', 'logo');
$mail->setBody('<img src="cid:logo"> Welcome!');
```

### Debug Mode

Enable debug output to troubleshoot SMTP issues:

```php
$mail->enableDebug(2);  // 0 = off, 1 = client messages, 2 = client and server messages
```

## Running Tests

```bash
composer install
./vendor/bin/phpunit tests/
```

## License

MIT License