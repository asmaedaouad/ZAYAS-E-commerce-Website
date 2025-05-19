<?php
// Include configuration
require_once '../../config/config.php';
require_once '../../config/Database.php';
require_once '../../controllers/PasswordResetController.php';

// Set page title
$pageTitle = 'Reset Password';
$customCss = 'login_register.css';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('/index.php');
}

// Redirect if reset email is not set
if (!isset($_SESSION['reset_email'])) {
    redirect('/views/auth/forgot_password.php');
}

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Create password reset controller
$passwordResetController = new PasswordResetController($db);

// Handle password reset
$data = $passwordResetController->resetPassword();
$errors = $data['errors'];
$verificationCode = $data['verification_code'];

// Include header
include_once '../../includes/header.php';
?>

<!-- Reset Password Section -->
<section class="hero-slider">
    <div class="form-container">
        <h2>Reset Password</h2>
        
        <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
        
        <p>We've sent a verification code to <strong><?php echo htmlspecialchars($_SESSION['reset_email']); ?></strong>. Enter the code below along with your new password.</p>
        
        <form id="resetPasswordForm" action="<?php echo url('/views/auth/reset_password.php'); ?>" method="post">
            <div class="form-group">
                <label for="verification_code">Verification Code</label>
                <input type="text" id="verification_code" name="verification_code" placeholder="Enter verification code" value="<?php echo htmlspecialchars($verificationCode); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="password">New Password</label>
                <input type="password" id="password" name="password" placeholder="Enter new password" required>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm new password" required>
            </div>
            
            <button type="submit" class="btn-primary">Reset Password</button>
            
            <div class="form-links">
                <p>Didn't receive the code? <a href="<?php echo url('/views/auth/forgot_password.php'); ?>">Request again</a></p>
            </div>
        </form>
    </div>
</section>

<?php
// Include footer
include_once '../../includes/footer.php';
?>
