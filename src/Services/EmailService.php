<?php

namespace Services;

/**
 * Email Service
 * Handles sending emails (verification, password reset, etc.)
 */
class EmailService
{
    private string $fromEmail;
    private string $fromName;
    private string $smtpHost;
    private int $smtpPort;
    private string $smtpUser;
    private string $smtpPass;
    private string $smtpEncryption;

    public function __construct()
    {
        // Load email config from environment (Docker Compose passes .env vars to container)
        $this->fromEmail      = getenv('SMTP_USER')       ?: 'noreply@camagru.local';
        $this->fromName       = getenv('SMTP_FROM_NAME')  ?: 'Camagru';
        $this->smtpHost       = getenv('SMTP_HOST')       ?: 'smtp.gmail.com';
        $this->smtpPort       = (int)(getenv('SMTP_PORT') ?: 465);          // 465 for Gmail SSL
        $this->smtpUser       = getenv('SMTP_USER')       ?: '';
        $this->smtpPass       = getenv('SMTP_PASS')       ?: '';
        $this->smtpEncryption = strtolower(getenv('SMTP_ENCRYPTION') ?: 'ssl'); // ssl or tls
    }

    /**
     * Send verification email
     *
     * @param string $toEmail
     * @param string $username
     * @param string $verificationToken
     * @return bool
     */
    public function sendVerificationEmail(string $toEmail, string $username, string $verificationToken): bool
    {
        error_log("sendVerificationEmail called for {$toEmail}");

        $appUrl = getenv('APP_URL') ?: 'http://localhost:8080';
        $verificationLink = $appUrl . '/verify?token=' . urlencode($verificationToken);

        $subject = "Verify your Camagru account";
        $message = $this->getVerificationEmailTemplate($username, $verificationLink);

        return $this->sendEmail($toEmail, $subject, $message);
    }

    /**
     * Send password reset email
     *
     * @param string $toEmail
     * @param string $username
     * @param string $resetToken
     * @return bool
     */
    public function sendPasswordResetEmail(string $toEmail, string $username, string $resetToken): bool
    {
        error_log("sendPasswordResetEmail called for {$toEmail}");

        $appUrl = getenv('APP_URL') ?: 'http://localhost:8080';
        $resetLink = $appUrl . '/user/reset-password?token=' . urlencode($resetToken);

        $subject = "Reset your Camagru password";
        $message = $this->getPasswordResetEmailTemplate($username, $resetLink);

        return $this->sendEmail($toEmail, $subject, $message);
    }

    /**
     * High-level send method with dev/prod behavior
     *
     * @param string $to
     * @param string $subject
     * @param string $message
     * @return bool
     */
    private function sendEmail(string $to, string $subject, string $message): bool
    {
        $env = getenv('APP_ENV') ?: 'development';

        // Development or missing SMTP config → log instead of sending
        if ($env === 'development' || empty($this->smtpUser) || empty($this->smtpPass)) {
            error_log("EMAIL TO: $to");
            error_log("SUBJECT: $subject");
            error_log("MESSAGE: $message");
            return true; // Simulate success in development
        }

        return $this->sendViaSmtp($to, $subject, $message);
    }

    /**
     * Low-level SMTP over SSL (Gmail) using PHP streams (no external libs)
     *
     * @param string $to
     * @param string $subject
     * @param string $message
     * @return bool
     */
    private function sendViaSmtp(string $to, string $subject, string $message): bool
    {
        error_log("sendViaSmtp starting for {$to}");
        
        $host       = $this->smtpHost;
        $port       = $this->smtpPort;
        $encryption = $this->smtpEncryption; // 'ssl' or 'tls'

        // For Gmail with port 465, use implicit SSL
        $remoteSocket = ($encryption === 'ssl' ? "ssl://" : "") . $host . ":" . $port;

        $errno  = 0;
        $errstr = '';
        $timeout = 30;

        $fp = stream_socket_client($remoteSocket, $errno, $errstr, $timeout, STREAM_CLIENT_CONNECT);
        if (!$fp) {
            error_log("SMTP connection failed: $errstr ($errno)");
            return false;
        }

        stream_set_timeout($fp, $timeout);

        $read = function () use ($fp): string {
            $data = '';
            while ($str = fgets($fp, 515)) {
                $data .= $str;
                // Lines that end with "code<space>" mean end of response (e.g. "250 OK")
                if (preg_match('/^\d{3} /', $str)) {
                    break;
                }
            }
            return $data;
        };

        $write = function (string $cmd) use ($fp): void {
            fwrite($fp, $cmd . "\r\n");
        };

        $expect = function (string $response, string $code): bool {
            if (substr($response, 0, 3) !== $code) {
                error_log("SMTP expected $code but got: " . trim($response));
                return false;
            }
            return true;
        };

        // 1. Server greeting
        $resp = $read();
        if (!$expect($resp, '220')) {
            fclose($fp);
            return false;
        }

        // 2. EHLO
        $write("EHLO localhost");
        $resp = $read();
        if (!$expect($resp, '250')) {
            fclose($fp);
            return false;
        }

        // (For STARTTLS on port 587, you'd issue STARTTLS here and enable crypto;
        // we keep it simple and rely on implicit SSL with port 465.)

        // 3. AUTH LOGIN
        $write("AUTH LOGIN");
        $resp = $read();
        if (!$expect($resp, '334')) {
            fclose($fp);
            return false;
        }

        // 4. Username (base64)
        $write(base64_encode($this->smtpUser));
        $resp = $read();
        if (!$expect($resp, '334')) {
            fclose($fp);
            return false;
        }

        // 5. Password (base64)
        $write(base64_encode($this->smtpPass));
        $resp = $read();
        if (!$expect($resp, '235')) {
            fclose($fp);
            return false;
        }

        // 6. MAIL FROM
        $from = $this->fromEmail;
        $write("MAIL FROM:<{$from}>");
        $resp = $read();
        if (!$expect($resp, '250')) {
            fclose($fp);
            return false;
        }

        // 7. RCPT TO
        $write("RCPT TO:<{$to}>");
        $resp = $read();
        if (!($expect($resp, '250') || $expect($resp, '251'))) {
            fclose($fp);
            return false;
        }

        // 8. DATA
        $write("DATA");
        $resp = $read();
        if (!$expect($resp, '354')) {
            fclose($fp);
            return false;
        }

        // 9. Build headers + body
        $headers = [
            'MIME-Version: 1.0',
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $this->fromName . ' <' . $this->fromEmail . '>',
            'Reply-To: ' . $this->fromEmail,
            'Subject: ' . $subject,
            'To: ' . $to,
        ];

        $data = implode("\r\n", $headers) . "\r\n\r\n" . $message . "\r\n.";

        // 10. Send message data
        $write($data);
        $resp = $read();
        if (!$expect($resp, '250')) {
            fclose($fp);
            return false;
        }

        // 11. QUIT
        $write("QUIT");
        fclose($fp);

        return true;
    }

    /**
     * Get verification email HTML template
     *
     * @param string $username
     * @param string $verificationLink
     * @return string
     */
    private function getVerificationEmailTemplate(string $username, string $verificationLink): string
    {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .button { display: inline-block; padding: 12px 24px; background: #2c3e50; color: white; text-decoration: none; border-radius: 5px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <h1>Welcome to Camagru, {$username}!</h1>
                <p>Thank you for registering. Please verify your email address by clicking the button below:</p>
                <p>
                    <a href='{$verificationLink}' class='button'>Verify Email Address</a>
                </p>
                <p>Or copy and paste this link into your browser:</p>
                <p><a href='{$verificationLink}'>{$verificationLink}</a></p>
                <p>If you didn't create an account, please ignore this email.</p>
            </div>
        </body>
        </html>
        ";
    }

    /**
     * Get password reset email HTML template
     *
     * @param string $username
     * @param string $resetLink
     * @return string
     */
    private function getPasswordResetEmailTemplate(string $username, string $resetLink): string
    {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .button { display: inline-block; padding: 12px 24px; background: #2c3e50; color: white; text-decoration: none; border-radius: 5px; }
                .warning { color: #d9534f; font-size: 14px; margin-top: 20px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <h1>Password Reset Request</h1>
                <p>Hi {$username},</p>
                <p>We received a request to reset your Camagru password. Click the button below to proceed:</p>
                <p>
                    <a href='{$resetLink}' class='button'>Reset Password</a>
                </p>
                <p>Or copy and paste this link into your browser:</p>
                <p><a href='{$resetLink}'>{$resetLink}</a></p>
                <p>This link will expire in 1 hour.</p>
                <p class='warning'><strong>If you didn't request a password reset, please ignore this email or contact support if you think your account may be compromised.</strong></p>
            </div>
        </body>
        </html>
        ";
    }
}