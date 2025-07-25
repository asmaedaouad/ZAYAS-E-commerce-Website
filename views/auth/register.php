<?php

require_once '../../config/config.php';
require_once '../../config/Database.php';
require_once '../../controllers/AuthController.php';


$pageTitle = 'Register';
$customCss = 'login_register.css';


if (isLoggedIn()) {
    redirect('/index.php');
}


$database = new Database();
$db = $database->getConnection();


$authController = new AuthController($db);


$data = $authController->register();
$errors = $data['errors'];
$firstName = $data['first_name'];
$lastName = $data['last_name'];
$email = $data['email'];


include_once '../../includes/header.php';
?>

<!-- Register Section -->
<section class="hero-slider">
    <div class="form-container">
        <h2>Create an Account</h2>

        <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <form id="registerForm" action="<?php echo url('/views/auth/register.php'); ?>" method="post">
            <div class="form-group">
                <label for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name" placeholder="Enter your first name" value="<?php echo htmlspecialchars($firstName); ?>" required>
            </div>

            <div class="form-group">
                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name" placeholder="Enter your last name" value="<?php echo htmlspecialchars($lastName); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
            </div>

            <button type="submit" class="btn-primary">Register</button>

            <div class="form-links">
                <p>Already have an account? <a href="<?php echo url('/views/auth/unified_login.php'); ?>">Login</a></p>
            </div>
        </form>
    </div>
</section>

<?php
// Include footer
include_once '../../includes/footer.php';
?>
