<?php

declare(strict_types=1);

namespace PhpMails;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * Mail class for sending emails using PHPMailer
 */
class Mail
{
    private PHPMailer $mailer;

    /**
     * Create a new Mail instance
     */
    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
    }

    /**
     * Configure SMTP settings
     *
     * @param string $host SMTP server hostname
     * @param int $port SMTP port (typically 587 for TLS, 465 for SSL)
     * @param string $username SMTP username
     * @param string $password SMTP password
     * @param string $encryption Encryption type ('tls', 'ssl', or '' for none)
     * @return self
     */
    public function configureSMTP(
        string $host,
        int $port,
        string $username,
        string $password,
        string $encryption = 'tls'
    ): self {
        $this->mailer->isSMTP();
        $this->mailer->Host = $host;
        $this->mailer->Port = $port;
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = $username;
        $this->mailer->Password = $password;

        if ($encryption === 'tls') {
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        } elseif ($encryption === 'ssl') {
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        }

        return $this;
    }

    /**
     * Set the sender address and name
     *
     * @param string $email Sender email address
     * @param string $name Sender name (optional)
     * @return self
     * @throws Exception
     */
    public function setFrom(string $email, string $name = ''): self
    {
        $this->mailer->setFrom($email, $name);
        return $this;
    }

    /**
     * Add a recipient
     *
     * @param string $email Recipient email address
     * @param string $name Recipient name (optional)
     * @return self
     * @throws Exception
     */
    public function addRecipient(string $email, string $name = ''): self
    {
        $this->mailer->addAddress($email, $name);
        return $this;
    }

    /**
     * Add a CC recipient
     *
     * @param string $email CC email address
     * @param string $name CC name (optional)
     * @return self
     * @throws Exception
     */
    public function addCC(string $email, string $name = ''): self
    {
        $this->mailer->addCC($email, $name);
        return $this;
    }

    /**
     * Add a BCC recipient
     *
     * @param string $email BCC email address
     * @param string $name BCC name (optional)
     * @return self
     * @throws Exception
     */
    public function addBCC(string $email, string $name = ''): self
    {
        $this->mailer->addBCC($email, $name);
        return $this;
    }

    /**
     * Add a reply-to address
     *
     * @param string $email Reply-to email address
     * @param string $name Reply-to name (optional)
     * @return self
     * @throws Exception
     */
    public function addReplyTo(string $email, string $name = ''): self
    {
        $this->mailer->addReplyTo($email, $name);
        return $this;
    }

    /**
     * Set the email subject
     *
     * @param string $subject Email subject
     * @return self
     */
    public function setSubject(string $subject): self
    {
        $this->mailer->Subject = $subject;
        return $this;
    }

    /**
     * Set the email body (HTML or plain text)
     *
     * @param string $body Email body content
     * @param bool $isHTML Whether the body is HTML (default: true)
     * @return self
     */
    public function setBody(string $body, bool $isHTML = true): self
    {
        $this->mailer->isHTML($isHTML);
        $this->mailer->Body = $body;
        return $this;
    }

    /**
     * Set alternative plain text body for non-HTML mail clients
     *
     * @param string $altBody Alternative plain text body
     * @return self
     */
    public function setAltBody(string $altBody): self
    {
        $this->mailer->AltBody = $altBody;
        return $this;
    }

    /**
     * Add an attachment
     *
     * @param string $path Path to the file
     * @param string $name Override the attachment name (optional)
     * @return self
     * @throws Exception
     */
    public function addAttachment(string $path, string $name = ''): self
    {
        $this->mailer->addAttachment($path, $name);
        return $this;
    }

    /**
     * Add an embedded image for use in HTML body
     *
     * @param string $path Path to the image
     * @param string $cid Content ID to reference in HTML (e.g., <img src="cid:logo">)
     * @param string $name Name to display (optional)
     * @return self
     * @throws Exception
     */
    public function addEmbeddedImage(string $path, string $cid, string $name = ''): self
    {
        $this->mailer->addEmbeddedImage($path, $cid, $name);
        return $this;
    }

    /**
     * Enable SMTP debugging
     *
     * @param int $level Debug level (0 = off, 1 = client, 2 = client and server)
     * @return self
     */
    public function enableDebug(int $level = SMTP::DEBUG_SERVER): self
    {
        $this->mailer->SMTPDebug = $level;
        return $this;
    }

    /**
     * Send the email
     *
     * @return bool True on success
     * @throws Exception On failure
     */
    public function send(): bool
    {
        return $this->mailer->send();
    }

    /**
     * Get the last error message
     *
     * @return string Error message
     */
    public function getError(): string
    {
        return $this->mailer->ErrorInfo;
    }

    /**
     * Clear all recipients, attachments, and reset the message
     *
     * @return self
     */
    public function reset(): self
    {
        $this->mailer->clearAddresses();
        $this->mailer->clearCCs();
        $this->mailer->clearBCCs();
        $this->mailer->clearReplyTos();
        $this->mailer->clearAttachments();
        $this->mailer->clearCustomHeaders();
        return $this;
    }

    /**
     * Get the underlying PHPMailer instance for advanced configuration
     *
     * @return PHPMailer
     */
    public function getMailer(): PHPMailer
    {
        return $this->mailer;
    }
}
