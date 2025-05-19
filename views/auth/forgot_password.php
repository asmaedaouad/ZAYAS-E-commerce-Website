<?php
// Include configuration
require_once '../../config/config.php';
require_once '../../config/Database.php';
require_once '../../controllers/PasswordResetController.php';

// Set page title
$pageTitle = 'Forgot Password';
$customCss = 'login_register.css';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('/index.php');
}

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Create password reset controller
$passwordResetController = new PasswordResetController($db);

// Handle password reset request
$data = $passwordResetController->requestReset();
$errors = $data['errors'];
$email = $data['email'];

// Check for reset message
$resetMessage = isset($_SESSION['reset_message']) ? $_SESSION['reset_message'] : '';
unset($_SESSION['reset_message']);

// Include header
include_once '../../includes/header.php';
?>

<!-- Forgot Password Section -->
<section class="hero-slider">
    <div class="form-container">
        <h2>Forgot Password</h2>
        
        <?php if (!empty($resetMessage)): ?>
        <div class="alert alert-success">
            <p><?php echo htmlspecialchars($resetMessage); ?></p>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
        
        <p>Enter your email address below and we'll send you a verification code to reset your password.</p>
        
        <form id="forgotPasswordForm" action="<?php echo url('/views/auth/forgot_password.php'); ?>" method="post">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>
            
            <button type="submit" class="btn-primary">Send Verification Code</button>
            
            <div class="form-links">
                <p>Remember your password? <a href="<?php echo url('/views/auth/unified_login.php'); ?>">Login</a></p>
            </div>
        </form>
    </div>
</section>

<?php
// Include footer
include_once '../../includes/footer.php';
?>
