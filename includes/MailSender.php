<?php
class MailSender {
    private $mailer;
    private $config;
    
    public function __construct() {
        // Load configuration
        $this->loadConfig();
        
        // Initialize PHPMailer
        $this->initializeMailer();
    }
    
    private function loadConfig() {
        $this->config = [
            'smtp' => [
                'host' => SMTP_HOST,
                'port' => SMTP_PORT,
                'username' => SMTP_USERNAME,
                'password' => SMTP_PASSWORD,
                'encryption' => SMTP_ENCRYPTION,
                'auth' => true
            ],
            'from' => [
                'email' => FROM_EMAIL,
                'name' => FROM_NAME
            ],
            'reply_to' => [
                'email' => REPLY_TO_EMAIL,
                'name' => REPLY_TO_NAME
            ],
            'debug' => DEBUG_MODE,
            'log_path' => LOG_PATH,
            'max_attachment_size' => MAX_ATTACHMENT_SIZE,
            'allowed_attachment_types' => ALLOWED_ATTACHMENT_TYPES
        ];
    }
    
    private function initializeMailer() {
        // Import PHPMailer classes
        $this->mailer = new PHPMailer\PHPMailer\PHPMailer(true);
        
        // Server settings
        $this->mailer->isSMTP();
        $this->mailer->Host = $this->config['smtp']['host'];
        $this->mailer->SMTPAuth = $this->config['smtp']['auth'];
        $this->mailer->Username = $this->config['smtp']['username'];
        $this->mailer->Password = $this->config['smtp']['password'];
        $this->mailer->SMTPSecure = $this->config['smtp']['encryption'];
        $this->mailer->Port = $this->config['smtp']['port'];
        
        // Debug mode
        if ($this->config['debug']) {
            $this->mailer->SMTPDebug = 2; // Enable verbose debug output
        }
        
        // Character set
        $this->mailer->CharSet = 'UTF-8';
        
        // From address
        $this->mailer->setFrom(
            $this->config['from']['email'],
            $this->config['from']['name']
        );
        
        // Reply-to address
        $this->mailer->addReplyTo(
            $this->config['reply_to']['email'],
            $this->config['reply_to']['name']
        );
    }
    
    public function send($to, $subject, $body, $isHtml = true, $attachments = []) {
        try {
            // Validate inputs
            $this->validateInputs($to, $subject, $body);
            
            // Check rate limiting
            $this->checkRateLimit();
            
            // Clear previous data
            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();
            $this->mailer->clearReplyTos();
            
            // Add reply-to (always add it back)
            $this->mailer->addReplyTo(
                $this->config['reply_to']['email'],
                $this->config['reply_to']['name']
            );
            
            // Add recipients
            $this->addRecipients($to);
            
            // Add attachments if any
            if (!empty($attachments)) {
                $this->addAttachments($attachments);
            }
            
            // Email content
            $this->mailer->isHTML($isHtml);
            $this->mailer->Subject = $this->sanitizeSubject($subject);
            
            if ($isHtml) {
                $this->mailer->Body = $this->sanitizeHtml($body);
                $this->mailer->AltBody = $this->htmlToPlainText($body);
            } else {
                $this->mailer->Body = $this->sanitizeText($body);
            }
            
            // Send email
            $result = $this->mailer->send();
            
            if ($result) {
                $this->logSuccess($to, $subject);
                $this->updateRateLimit();
            }
            
            return $result;
            
        } catch (Exception $e) {
            $error = "Mailer Error: " . $e->getMessage();
            $this->logError($error, $to, $subject);
            throw new Exception($error);
        }
    }
    
    private function validateInputs($to, $subject, $body) {
        // Validate recipient(s)
        if (is_string($to)) {
            $to = [$to];
        }
        
        foreach ($to as $email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Invalid email address: $email");
            }
        }
        
        // Validate subject
        $subject = trim($subject);
        if (empty($subject)) {
            throw new Exception("Email subject cannot be empty");
        }
        
        if (strlen($subject) > 255) {
            throw new Exception("Email subject is too long");
        }
        
        // Validate body
        $body = trim($body);
        if (empty($body)) {
            throw new Exception("Email body cannot be empty");
        }
        
        // Check for email injection
        $this->checkInjection($subject . $body);
    }
    
    private function addRecipients($to) {
        if (is_string($to)) {
            $to = [$to];
        }
        
        foreach ($to as $email) {
            $this->mailer->addAddress($email);
        }
    }
    
    private function addAttachments($attachments) {
        foreach ($attachments as $attachment) {
            if (is_string($attachment)) {
                $this->validateAttachment($attachment);
                $this->mailer->addAttachment($attachment);
            } elseif (is_array($attachment)) {
                $this->validateAttachment($attachment['path']);
                $this->mailer->addAttachment(
                    $attachment['path'],
                    $attachment['name'] ?? '',
                    $attachment['encoding'] ?? 'base64',
                    $attachment['type'] ?? ''
                );
            }
        }
    }
    
    private function validateAttachment($filePath) {
        if (!file_exists($filePath)) {
            throw new Exception("Attachment file not found: $filePath");
        }
        
        // Check file size
        $fileSize = filesize($filePath);
        if ($fileSize > $this->config['max_attachment_size']) {
            throw new Exception("Attachment file too large: $filePath");
        }
        
        // Check file type
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        if (!in_array($extension, $this->config['allowed_attachment_types'])) {
            throw new Exception("Attachment type not allowed: $extension");
        }
    }
    
    private function checkInjection($input) {
        $suspiciousPatterns = [
            '/content-type:/i',
            '/bcc:/i',
            '/cc:/i',
            '/to:/i',
            '/from:/i',
            '/mime-version:/i',
            '/multipart\/mixed/i',
            '/Content-Transfer-Encoding:/i',
            '/\r|\n/'
        ];
        
        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $input)) {
                throw new Exception("Potential email injection detected");
            }
        }
    }
    
    private function sanitizeSubject($subject) {
        return htmlspecialchars(trim($subject), ENT_QUOTES, 'UTF-8');
    }
    
    private function sanitizeHtml($html) {
        // Basic HTML sanitization
        $allowedTags = '<p><br><strong><em><ul><ol><li><a><span><div><h1><h2><h3><h4><h5><h6>';
        $sanitized = strip_tags($html, $allowedTags);
        
        // Remove potentially dangerous attributes
        $sanitized = preg_replace('/<([a-z][a-z0-9]*)[^>]*?(javascript:.*?)([^>]*?)>/i', '<$1$3>', $sanitized);
        
        return $sanitized;
    }
    
    private function sanitizeText($text) {
        return htmlspecialchars(trim($text), ENT_QUOTES, 'UTF-8');
    }
    
    private function htmlToPlainText($html) {
        $text = strip_tags($html);
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }
    
    private function checkRateLimit() {
        $rateFile = $this->config['log_path'] . '.rate';
        $currentHour = date('Y-m-d-H');
        
        if (file_exists($rateFile)) {
            $data = json_decode(file_get_contents($rateFile), true);
            
            // Check if it's the same hour
            if ($data['hour'] === $currentHour) {
                if ($data['count'] >= MAX_EMAILS_PER_HOUR) {
                    throw new Exception("Rate limit exceeded. Maximum " . MAX_EMAILS_PER_HOUR . " emails per hour allowed.");
                }
            }
        }
    }
    
    private function updateRateLimit() {
        $rateFile = $this->config['log_path'] . '.rate';
        $currentHour = date('Y-m-d-H');
        
        if (file_exists($rateFile)) {
            $data = json_decode(file_get_contents($rateFile), true);
            
            if ($data['hour'] === $currentHour) {
                $data['count']++;
            } else {
                $data = ['hour' => $currentHour, 'count' => 1];
            }
        } else {
            $data = ['hour' => $currentHour, 'count' => 1];
        }
        
        file_put_contents($rateFile, json_encode($data));
    }
    
    private function logSuccess($to, $subject) {
        $timestamp = date('Y-m-d H:i:s');
        $recipients = is_array($to) ? implode(', ', $to) : $to;
        $message = "[SUCCESS] $timestamp | To: $recipients | Subject: $subject";
        error_log($message . PHP_EOL, 3, $this->config['log_path']);
    }
    
    private function logError($error, $to = '', $subject = '') {
        $timestamp = date('Y-m-d H:i:s');
        $message = "[ERROR] $timestamp | Error: $error";
        
        if (!empty($to)) {
            $recipients = is_array($to) ? implode(', ', $to) : $to;
            $message .= " | To: $recipients";
        }
        
        if (!empty($subject)) {
            $message .= " | Subject: $subject";
        }
        
        error_log($message . PHP_EOL, 3, $this->config['log_path']);
    }
}
?>