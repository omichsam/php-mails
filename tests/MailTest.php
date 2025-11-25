<?php

declare(strict_types=1);

namespace PhpMails\Tests;

use PHPUnit\Framework\TestCase;
use PHPMailer\PHPMailer\PHPMailer;
use PhpMails\Mail;

/**
 * Test cases for the Mail class
 */
class MailTest extends TestCase
{
    private Mail $mail;

    protected function setUp(): void
    {
        $this->mail = new Mail();
    }

    public function testCanCreateMailInstance(): void
    {
        $this->assertInstanceOf(Mail::class, $this->mail);
    }

    public function testGetMailerReturnsPhpMailerInstance(): void
    {
        $mailer = $this->mail->getMailer();
        $this->assertInstanceOf(PHPMailer::class, $mailer);
    }

    public function testConfigureSMTPReturnsSelf(): void
    {
        $result = $this->mail->configureSMTP(
            'smtp.example.com',
            587,
            'user@example.com',
            'password',
            'tls'
        );
        $this->assertSame($this->mail, $result);
    }

    public function testConfigureSMTPSetsCorrectValues(): void
    {
        $this->mail->configureSMTP(
            'smtp.test.com',
            465,
            'testuser',
            'testpass',
            'ssl'
        );

        $mailer = $this->mail->getMailer();
        $this->assertEquals('smtp.test.com', $mailer->Host);
        $this->assertEquals(465, $mailer->Port);
        $this->assertEquals('testuser', $mailer->Username);
        $this->assertEquals('testpass', $mailer->Password);
        $this->assertTrue($mailer->SMTPAuth);
    }

    public function testSetFromReturnsSelf(): void
    {
        $result = $this->mail->setFrom('sender@example.com', 'Sender Name');
        $this->assertSame($this->mail, $result);
    }

    public function testAddRecipientReturnsSelf(): void
    {
        $result = $this->mail->addRecipient('recipient@example.com', 'Recipient Name');
        $this->assertSame($this->mail, $result);
    }

    public function testAddCCReturnsSelf(): void
    {
        $result = $this->mail->addCC('cc@example.com', 'CC Name');
        $this->assertSame($this->mail, $result);
    }

    public function testAddBCCReturnsSelf(): void
    {
        $result = $this->mail->addBCC('bcc@example.com', 'BCC Name');
        $this->assertSame($this->mail, $result);
    }

    public function testAddReplyToReturnsSelf(): void
    {
        $result = $this->mail->addReplyTo('reply@example.com', 'Reply Name');
        $this->assertSame($this->mail, $result);
    }

    public function testSetSubjectReturnsSelf(): void
    {
        $result = $this->mail->setSubject('Test Subject');
        $this->assertSame($this->mail, $result);
    }

    public function testSetSubjectSetsCorrectValue(): void
    {
        $this->mail->setSubject('My Test Subject');
        $mailer = $this->mail->getMailer();
        $this->assertEquals('My Test Subject', $mailer->Subject);
    }

    public function testSetBodyReturnsSelf(): void
    {
        $result = $this->mail->setBody('<p>Test body</p>');
        $this->assertSame($this->mail, $result);
    }

    public function testSetBodySetsCorrectValue(): void
    {
        $this->mail->setBody('<h1>Hello</h1>');
        $mailer = $this->mail->getMailer();
        $this->assertEquals('<h1>Hello</h1>', $mailer->Body);
    }

    public function testSetAltBodyReturnsSelf(): void
    {
        $result = $this->mail->setAltBody('Plain text body');
        $this->assertSame($this->mail, $result);
    }

    public function testSetAltBodySetsCorrectValue(): void
    {
        $this->mail->setAltBody('Plain text alternative');
        $mailer = $this->mail->getMailer();
        $this->assertEquals('Plain text alternative', $mailer->AltBody);
    }

    public function testEnableDebugReturnsSelf(): void
    {
        $result = $this->mail->enableDebug(2);
        $this->assertSame($this->mail, $result);
    }

    public function testEnableDebugSetsCorrectValue(): void
    {
        $this->mail->enableDebug(2);
        $mailer = $this->mail->getMailer();
        $this->assertEquals(2, $mailer->SMTPDebug);
    }

    public function testResetReturnsSelf(): void
    {
        $result = $this->mail->reset();
        $this->assertSame($this->mail, $result);
    }

    public function testResetClearsRecipients(): void
    {
        $this->mail->addRecipient('test1@example.com');
        $this->mail->addRecipient('test2@example.com');
        $this->mail->addCC('cc@example.com');
        $this->mail->addBCC('bcc@example.com');

        $mailer = $this->mail->getMailer();
        $this->assertNotEmpty($mailer->getToAddresses());

        $this->mail->reset();

        $this->assertEmpty($mailer->getToAddresses());
        $this->assertEmpty($mailer->getCcAddresses());
        $this->assertEmpty($mailer->getBccAddresses());
    }

    public function testFluentInterface(): void
    {
        $result = $this->mail
            ->setFrom('sender@example.com', 'Sender')
            ->addRecipient('recipient@example.com', 'Recipient')
            ->setSubject('Test Email')
            ->setBody('<p>Hello World</p>')
            ->setAltBody('Hello World');

        $this->assertSame($this->mail, $result);

        $mailer = $this->mail->getMailer();
        $this->assertEquals('Test Email', $mailer->Subject);
        $this->assertEquals('<p>Hello World</p>', $mailer->Body);
        $this->assertEquals('Hello World', $mailer->AltBody);
    }

    public function testGetErrorReturnsString(): void
    {
        $error = $this->mail->getError();
        $this->assertIsString($error);
    }
}
