<?php
namespace App\Core;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService {
    
    /**
     * Sends an email using PHPMailer and the configured SMTP server.
     */
    public static function send($toEmail, $toName, $subject, $htmlContent) {
        $mail = new PHPMailer(true);

        try {
            // Server Settings
            $mail->isSMTP();
            $mail->Host       = SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USER;
            $mail->Password   = SMTP_PASS;
            $mail->SMTPSecure = SMTP_SECURE;
            $mail->Port       = SMTP_PORT;

            // Recipients
            $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
            $mail->addAddress($toEmail, $toName);

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = self::getHtmlTemplate($subject, $htmlContent);

            $mail->send();
            return true;
        } catch (Exception $e) {
            // Log the error in production instead of breaking execution
            error_log("PHPMailer Error: " . $mail->ErrorInfo);
            return false;
        }
    }

    /**
     * Wraps raw email content in a premium HTML responsive template
     */
    private static function getHtmlTemplate($title, $body) {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>' . htmlspecialchars($title) . '</title>
            <style>
                body { font-family: \'Segoe UI\', Helvetica, Arial, sans-serif; background-color: #f7f9fc; color: #333333; margin: 0; padding: 0; }
                .email-container { max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; border: 1px solid #e1e8ed; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
                .email-header { background: linear-gradient(135deg, #0d6efd, #023e8a); color: #ffffff; padding: 30px; text-align: center; }
                .email-header h1 { margin: 0; font-size: 24px; font-weight: 600; letter-spacing: 0.5px; }
                .email-body { padding: 30px; line-height: 1.6; color: #495057; }
                .email-body h2 { color: #212529; font-size: 20px; margin-top: 0; }
                .email-footer { background-color: #f1f3f5; color: #868e96; text-align: center; padding: 15px; font-size: 12px; border-top: 1px solid #e9ecef; }
                .btn { display: inline-block; padding: 10px 20px; margin-top: 15px; background-color: #0d6efd; color: #ffffff; text-decoration: none; border-radius: 4px; font-weight: bold; }
                .info-box { background-color: #f8f9fa; border-left: 4px solid #0d6efd; padding: 15px; margin: 20px 0; border-radius: 0 4px 4px 0; }
            </style>
        </head>
        <body>
            <div class="email-container">
                <div class="email-header">
                    <h1>AidFlow</h1>
                </div>
                <div class="email-body">
                    ' . $body . '
                </div>
                <div class="email-footer">
                    &copy; ' . date('Y') . ' AidFlow Association. All rights reserved.<br>
                    This is an automated system notification. Please do not reply directly to this email.
                </div>
            </div>
        </body>
        </html>';
    }

    // Specific Templates for Welfare Management System

    public static function sendWelcomeEmail($email, $name, $username) {
        $subject = "Welcome to AidFlow Welfare System!";
        $body = "
            <h2>Hello " . htmlspecialchars($name) . ",</h2>
            <p>Thank you for registering on AidFlow, the Welfare Management System. Your account request has been successfully received.</p>
            <div class='info-box'>
                <strong>Login Details:</strong><br>
                Username: " . htmlspecialchars($username) . "<br>
                Account Status: <strong>Pending Approval</strong>
            </div>
            <p>Our Welfare administrators are currently reviewing your registration. You will receive another notification once your account has been approved and activated.</p>
        ";
        return self::send($email, $name, $subject, $body);
    }

    public static function sendAccountApprovalEmail($email, $name) {
        $subject = "AidFlow Account Approved & Activated!";
        $body = "
            <h2>Hello " . htmlspecialchars($name) . ",</h2>
            <p>We are pleased to inform you that your AidFlow member account has been approved and activated by the administrator!</p>
            <p>You can now log in to the portal to manage your profile, view contribution history, and submit welfare assistance requests.</p>
            <p><a href='http://localhost/AidFlow/auth/login' class='btn' style='color:#ffffff;'>Log In Now</a></p>
        ";
        return self::send($email, $name, $subject, $body);
    }

    public static function sendWelfareSubmissionEmail($email, $name, $requestTitle, $amount) {
        $subject = "Welfare Request Submitted: " . $requestTitle;
        $body = "
            <h2>Hello " . htmlspecialchars($name) . ",</h2>
            <p>Your welfare assistance request has been received and is now in our verification queue.</p>
            <div class='info-box'>
                <strong>Request Summary:</strong><br>
                Title: " . htmlspecialchars($requestTitle) . "<br>
                Amount: $" . number_format($amount, 2) . "<br>
                Status: <strong>Pending Review</strong>
            </div>
            <p>Our Welfare Officer will inspect your supporting documents. You can log into your dashboard at any time to monitor progress.</p>
        ";
        return self::send($email, $name, $subject, $body);
    }

    public static function sendWelfareApprovalEmail($email, $name, $requestTitle, $amount) {
        $subject = "Welfare Request Approved!";
        $body = "
            <h2>Congratulations " . htmlspecialchars($name) . ",</h2>
            <p>Your welfare request for <strong>" . htmlspecialchars($requestTitle) . "</strong> has been approved.</p>
            <div class='info-box'>
                <strong>Approved Assistance:</strong><br>
                Request: " . htmlspecialchars($requestTitle) . "<br>
                Approved Amount: $" . number_format($amount, 2) . "<br>
                Status: <strong>Approved - Awaiting Disbursement</strong>
            </div>
            <p>The Treasurer has been notified to execute the disbursement. The funds will be released shortly.</p>
        ";
        return self::send($email, $name, $subject, $body);
    }

    public static function sendWelfareRejectionEmail($email, $name, $requestTitle, $reason) {
        $subject = "Update on your Welfare Request";
        $body = "
            <h2>Hello " . htmlspecialchars($name) . ",</h2>
            <p>Your welfare request <strong>" . htmlspecialchars($requestTitle) . "</strong> has been reviewed.</p>
            <div class='info-box' style='border-left-color: #dc3545;'>
                <strong>Rejection Reason:</strong><br>
                " . htmlspecialchars($reason) . "
            </div>
            <p>If you believe this is in error, or you need to supply updated supporting documentation, please contact the Welfare Officer or resubmit your request through the member portal.</p>
        ";
        return self::send($email, $name, $subject, $body);
    }

    public static function sendContributionReminder($email, $name, $monthName, $amount) {
        $subject = "AidFlow Contribution Reminder - " . $monthName;
        $body = "
            <h2>Hello " . htmlspecialchars($name) . ",</h2>
            <p>This is a friendly reminder that your monthly contribution for <strong>" . htmlspecialchars($monthName) . "</strong> is currently outstanding.</p>
            <div class='info-box'>
                <strong>Amount Due:</strong> $" . number_format($amount, 2) . "
            </div>
            <p>Please make your payment via bank transfer or mobile money and submit the details to the Treasurer to receive your receipt.</p>
        ";
        return self::send($email, $name, $subject, $body);
    }

    public static function sendDisbursementEmail($email, $name, $requestTitle, $amount, $ref) {
        $subject = "Funds Disbursed: " . $requestTitle;
        $body = "
            <h2>Hello " . htmlspecialchars($name) . ",</h2>
            <p>We are pleased to notify you that the funds for your approved welfare request have been successfully disbursed.</p>
            <div class='info-box' style='border-left-color: #198754;'>
                <strong>Payment Details:</strong><br>
                Request: " . htmlspecialchars($requestTitle) . "<br>
                Disbursed Amount: $" . number_format($amount, 2) . "<br>
                Reference: " . htmlspecialchars($ref) . "
            </div>
            <p>The funds should be available in your account shortly. Thank you!</p>
        ";
        return self::send($email, $name, $subject, $body);
    }

    public static function sendPasswordReset($email, $name, $token) {
        $subject = "AidFlow Password Reset Request";
        $resetLink = "http://localhost/AidFlow/auth/reset_password/" . urlencode($token);
        $body = "
            <h2>Hello " . htmlspecialchars($name) . ",</h2>
            <p>We received a request to reset the password for your AidFlow account. Click the button below to choose a new password. This link is valid for 1 hour.</p>
            <p><a href='" . $resetLink . "' class='btn' style='color:#ffffff;'>Reset Password</a></p>
            <p>If you did not request this, please ignore this email. Your password will remain unchanged.</p>
        ";
        return self::send($email, $name, $subject, $body);
    }
}
