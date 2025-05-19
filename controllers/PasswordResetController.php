<?php
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class PasswordResetController {
    private $userModel;
    private $db;

    public function __construct($db) {
        $this->db = $db;
        $this->userModel = new UserModel($db);
    }

    // Request password reset
    public function requestReset() {
        // Check if form is submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get form data
            $email = isset($_POST['email']) ? sanitize($_POST['email']) : '';

            // Validate input
            $errors = [];

            if (empty($email)) {
                $errors[] = 'Email is required';
            } elseif (!preg_match('/^[a-zA-Z][a-zA-Z0-9._%+-]*@[a-zA-Z]+\.[a-zA-Z]{2,}$/', $email)) {
                $errors[] = 'Invalid email format. Email must be like: user@example.com';
            }

            // If no errors, check if email exists
            if (empty($errors)) {
                $user = $this->userModel->getUserByEmail($email);

                if ($user) {
                    // Generate a random 6-character verification code
                    $verificationCode = $this->generateVerificationCode();

                    // Store the verification code in the database
                    if ($this->userModel->createPasswordResetToken($user['id'], $verificationCode)) {
                        // Send verification email
                        if ($this->sendVerificationEmail($user, $verificationCode)) {
                            // Store email in session for the reset form
                            $_SESSION['reset_email'] = $email;
                            
                            // Redirect to verification code page
                            redirect('/views/auth/reset_password.php');
                        } else {
                            $errors[] = 'Failed to send verification email. Please try again.';
                        }
                    } else {
                        $errors[] = 'Failed to create verification code. Please try again.';
                    }
                } else {
                    // Don't reveal that the email doesn't exist for security reasons
                    // Instead, show a generic success message
                    $_SESSION['reset_message'] = 'If your email exists in our system, you will receive a verification code shortly.';
                    redirect('/views/auth/forgot_password.php');
                }
            }

            // If we get here, there were errors
            return [
                'errors' => $errors,
                'email' => $email
            ];
        }

        // Display request form
        return [
            'errors' => [],
            'email' => ''
        ];
    }

    // Reset password with verification code
    public function resetPassword() {
        // Check if form is submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get form data
            $verificationCode = isset($_POST['verification_code']) ? sanitize($_POST['verification_code']) : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';
            $confirmPassword = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

            // Validate input
            $errors = [];

            if (empty($verificationCode)) {
                $errors[] = 'Verification code is required';
            }

            if (empty($password)) {
                $errors[] = 'Password is required';
            } elseif (strlen($password) < 6) {
                $errors[] = 'Password must be at least 6 characters';
            }

            if ($password !== $confirmPassword) {
                $errors[] = 'Passwords do not match';
            }

            // If no errors, verify code and reset password
            if (empty($errors)) {
                if ($this->userModel->resetPasswordWithToken($verificationCode, $password)) {
                    // Set success message
                    $_SESSION['login_message'] = 'Password has been reset successfully. You can now login with your new password.';
                    
                    // Redirect to login page
                    redirect('/views/auth/unified_login.php');
                } else {
                    $errors[] = 'Invalid or expired verification code. Please request a new one.';
                }
            }

            // If we get here, there were errors
            return [
                'errors' => $errors,
                'verification_code' => $verificationCode
            ];
        }

        // Display reset form
        return [
            'errors' => [],
            'verification_code' => ''
        ];
    }

    // Generate a random 6-character verification code
    private function generateVerificationCode() {
        // Generate a random 6-character code (alphanumeric)
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $code = '';
        
        for ($i = 0; $i < 6; $i++) {
            $code .= $characters[rand(0, strlen($characters) - 1)];
        }
        
        return $code;
    }

    // Send verification email
    private function sendVerificationEmail($user, $verificationCode) {
        // Create a new PHPMailer instance
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'zaynabaitaddi2022@gmail.com';
            $mail->Password = 'zhoc ksww yysj sgrl';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            
            // Disable SSL certificate verification (only for testing)
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            // Recipients
            $mail->setFrom('zaynabaitaddi2022@gmail.com', 'ZAYAS');
            $mail->addAddress($user['email'], $user['first_name'] . ' ' . $user['last_name']);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Verification Code';
            
            // Email body
            $htmlBody = '
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">
                <h2 style="color: #8B4513; text-align: center;">Password Reset Verification Code</h2>
                <p>Hello ' . htmlspecialchars($user['first_name']) . ',</p>
                <p>You have requested to reset your password. Please use the following verification code to complete the process:</p>
                <div style="text-align: center; margin: 30px 0;">
                    <div style="font-size: 24px; letter-spacing: 5px; font-weight: bold; background-color: #f5f5f5; padding: 15px; border-radius: 5px; display: inline-block;">' . $verificationCode . '</div>
                </div>
                <p>This code will expire in 15 minutes.</p>
                <p>If you did not request this code, please ignore this email and your password will remain unchanged.</p>
                <p style="margin-top: 30px; font-size: 12px; color: #777; text-align: center;">This is an automated message, please do not reply.</p>
            </div>';
            
            $plainTextBody = "Hello " . $user['first_name'] . ",\n\n" .
                            "You have requested to reset your password. Please use the following verification code to complete the process:\n\n" .
                            $verificationCode . "\n\n" .
                            "This code will expire in 15 minutes.\n\n" .
                            "If you did not request this code, please ignore this email and your password will remain unchanged.";
            
            $mail->Body = $htmlBody;
            $mail->AltBody = $plainTextBody;

            // Send email
            $mail->send();
            
            return true;
        } catch (Exception $e) {
            // Log the error (in a real application)
            error_log('Email could not be sent. Mailer Error: ' . $mail->ErrorInfo);
            
            return false;
        }
    }
}
?>
