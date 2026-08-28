<?php
/**
 * mailer.php
 * ----------
 * Simple transactional email helper.
 *
 * Out of the box this uses PHP's built-in mail(). On XAMPP that requires
 * an SMTP agent (e.g. Mercury, which ships with XAMPP) or php.ini SMTP
 * settings, otherwise emails will not send. To use a real provider, either:
 *
 *   1. Configure XAMPP -> PHP -> php.ini [mail function] with your SMTP
 *      server, OR
 *   2. Swap send_email() to use PHPMailer with your SMTP credentials.
 *
 * The functions below never throw — email problems are logged silently so
 * they never break the booking/payment flow.
 */

// Sender identity shown in outgoing confirmations. Edit to fit your brand.
if (!defined('MAIL_FROM'))      define('MAIL_FROM', 'no-reply@olowocorp.com');
if (!defined('MAIL_FROM_NAME'))  define('MAIL_FROM_NAME', 'OLOWO Corp');

/**
 * Send a plain-text email.
 *
 * @param string $to      Recipient address.
 * @param string $subject Email subject.
 * @param string $body    Plain-text body.
 * @return bool True if PHP accepted the message for delivery.
 */
function send_email(string $to, string $subject, string $body): bool {
    if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $headers .= 'From: ' . MAIL_FROM_NAME . ' <' . MAIL_FROM . ">\r\n";
    $headers .= 'Reply-To: ' . MAIL_FROM . "\r\n";
    $headers .= 'X-Mailer: PHP/' . phpversion();

    try {
        return mail($to, $subject, $body, $headers);
    } catch (\Throwable $e) {
        error_log('send_email failed: ' . $e->getMessage());
        return false;
    }
}

/**
 * Build a small, consistent footer used in all confirmation emails.
 */
function email_footer(): string {
    return "\n\n--\n" . MAIL_FROM_NAME . "\n" .
           'Support: ' . MAIL_FROM . "\n" .
           'https://localhost/travel-agent/index.php';
}
