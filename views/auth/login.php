<?php
// Include configuration
require_once '../../config/config.php';
require_once '../../config/Database.php';
require_once '../../controllers/AuthController.php';

// Set page title
$pageTitle = 'Login';
$customCss = 'login_register.css';

// Redirect if already logged in
if (isLoggedIn()) {
    // Redirect based on user type
    if (isAdmin()) {
        redirect('/admin/dashboard.php');
    } elseif (isDelivery()) {
        redirect('/delivery/dashboard.php');
    } else {
        redirect('/index.php');
    }
}

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Create auth controller
$authController = new AuthController($db);

// Handle login
$data = $authController->login();
$errors = $data['errors'];
$email = $data['email'];

// Check if user just registered
$registered = isset($_GET['registered']) && $_GET['registered'] == 1;

// Include header
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

        <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <form id="loginForm" action="<?php echo url('/views/auth/login.php'); ?>" method="post">
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
