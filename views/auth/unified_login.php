<?php

require_once '../../config/config.php';
require_once '../../config/Database.php';
require_once '../../controllers/AuthController.php';


$pageTitle = 'Login';
$customCss = 'login_register.css';


if (isLoggedIn()) {
    
    if (isAdmin()) {
        redirect('/admin/dashboard.php');
    } elseif (isDelivery()) {
        redirect('/delivery/dashboard.php');
    } else {
        redirect('/index.php');
    }
}


$database = new Database();
$db = $database->getConnection();


$authController = new AuthController($db);


$data = $authController->login();
$errors = $data['errors'];
$email = $data['email'];

// Check if user just registered
$registered = isset($_GET['registered']) && $_GET['registered'] == 1;

include_once '../../includes/header.php';
?>

<!-- Login Section -->
<section class="hero-slider">
    <div class="form-container">
        <h2>Login to Your Account</h2>

        <?php if ($registered): ?>
        <div class="alert alert-success">
            <p>Registration successful! Please login with your credentials.</p>
        </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['login_message'])): ?>
        <div class="alert alert-success">
            <p><?php echo htmlspecialchars($_SESSION['login_message']); ?></p>
        </div>
        <?php unset($_SESSION['login_message']); ?>
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

        <form id="loginForm" action="<?php echo url('/views/auth/unified_login.php'); ?>" method="post">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>

            <button type="submit" class="btn-primary">Login</button>

            <div class="form-links">
                <a href="<?php echo url('/views/auth/forgot_password.php'); ?>">Forgot Password?</a>
            </div>

            <div class="form-links">
                <p>Don't have an account? <a href="<?php echo url('/views/auth/register.php'); ?>">Register</a></p>
            </div>
        </form>
    </div>
</section>

<?php
// Include footer
include_once '../../includes/footer.php';
?>
