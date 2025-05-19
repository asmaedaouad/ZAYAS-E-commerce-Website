<?php
// Include configuration
require_once '../config/config.php';
require_once '../vendor/autoload.php';

// Import PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $email = isset($_POST['email']) ? sanitize($_POST['email']) : '';

    // Validate input
    $errors = [];

    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format';
    }

    // If no errors, send verification code
    if (empty($errors)) {
        // Generate a random 6-digit verification code
        $verificationCode = sprintf('%06d', mt_rand(0, 999999));

        // Store the verification code in session (in a real app, you'd store this in a database with an expiration time)
        $_SESSION['verification_code'] = $verificationCode;
        $_SESSION['verification_email'] = $email;
        $_SESSION['verification_time'] = time(); // To check expiration later

        // Create a new PHPMailer instance
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Enable verbose debug output to see what's happening
            $mail->isSMTP(); // Send using SMTP
            $mail->Host = 'smtp.gmail.com'; // Set the SMTP server to send through
            $mail->SMTPAuth = true; // Enable SMTP authentication
            $mail->Username = 'zaynabaitaddi2022@gmail.com'; // SMTP username
            $mail->Password = 'zhoc ksww yysj sgrl'; // App password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Use TLS encryption
            $mail->Port = 587; // Use port 587 for TLS

            // Disable SSL certificate verification (only for testing)
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            // Recipients
            $mail->setFrom('zaynabaitaddi2022@gmail.com', 'ZAYAS Simple');
            $mail->addAddress($email); // Add a recipient

            // Content
            $mail->isHTML(true); // Set email format to HTML
            $mail->Subject = 'Your Verification Code';

            // Email body
            $htmlBody = '
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">
                <h2 style="color: #8B4513; text-align: center;">Verification Code</h2>
                <p>You have requested a verification code. Please use the following code to complete your verification:</p>
                <div style="text-align: center; margin: 30px 0;">
                    <div style="font-size: 24px; letter-spacing: 5px; font-weight: bold; background-color: #f5f5f5; padding: 15px; border-radius: 5px; display: inline-block;">' . $verificationCode . '</div>
                </div>
                <p>This code will expire in 15 minutes.</p>
                <p>If you did not request this code, please ignore this email.</p>
                <p style="margin-top: 30px; font-size: 12px; color: #777; text-align: center;">This is an automated message, please do not reply.</p>
            </div>';

            $plainTextBody = "Your verification code is: $verificationCode\n\nThis code will expire in 15 minutes.\n\nIf you did not request this code, please ignore this email.";

            $mail->Body = $htmlBody;
            $mail->AltBody = $plainTextBody; // Plain text version

            // Send email
            $mail->send();

            // Set success message
            $_SESSION['email_success'] = 'Verification code has been sent to your email!';
        } catch (Exception $e) {
            // Set error message
            $_SESSION['email_error'] = "Verification code could not be sent. Mailer Error: {$mail->ErrorInfo}";

            // Clear verification data on error
            unset($_SESSION['verification_code']);
            unset($_SESSION['verification_email']);
            unset($_SESSION['verification_time']);
        }
    } else {
        // Set error messages
        $_SESSION['email_error'] = implode('<br>', $errors);
    }
}

// Redirect back to the verification form
redirect('/email_test/verification_code.php');
?>
