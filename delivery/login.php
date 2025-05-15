<?php
// Include configuration
require_once '../config/config.php';
require_once '../config/Database.php';
require_once './controllers/AuthController.php';

// Check if user is already logged in
if (isLoggedIn() && isDelivery()) {
    redirect('/delivery/dashboard.php');
}

// Set page title
$pageTitle = 'Login';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Create auth controller
$authController = new AuthController($db);

// Process login
$data = $authController->login();
$errors = $data['errors'];
$email = $data['email'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ZAYAS Delivery</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo url('/delivery/public/css/style.css'); ?>">
</head>
<body class="bg-light">
    <div class="container">
        <div class="login-container">
            <div class="login-logo">
                <div class="d-flex align-items-center justify-content-center">
                    <div class="status-icon" style="background-color: var(--in-transit-color); width: 60px; height: 60px;">
                        <i class="fas fa-truck fa-lg"></i>
                    </div>
                </div>
                <h2>ZAYAS Delivery</h2>
                <p class="text-muted">Delivery Personnel Portal</p>
            </div>

            <div class="card login-card">
                <div class="card-header">
                    <i class="fas fa-sign-in-alt me-2"></i> Login to Your Account
                </div>
                <div class="card-body">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo $error; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" placeholder="Enter your email" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                            </div>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-brown btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i> Login
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <a href="<?php echo url('/index.php'); ?>" class="text-decoration-none">
                        <i class="fas fa-home me-1"></i> Back to Main Site
                    </a>
                </div>
            </div>

            <div class="text-center mt-4 text-muted">
                <small>&copy; <?php echo date('Y'); ?> ZAYAS. All rights reserved.</small>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
