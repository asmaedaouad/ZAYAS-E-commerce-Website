<?php
// Include configuration
require_once '../../config/config.php';
require_once '../../config/Database.php';
require_once '../../controllers/UserController.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    redirect('/views/auth/login.php');
}

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Create user controller
$userController = new UserController($db);

// Handle update profile
$result = $userController->updateProfile();

// Set success or error message in session
if (isset($result['success'])) {
    $_SESSION['profile_success'] = $result['success'];
} elseif (isset($result['errors'])) {
    $_SESSION['profile_errors'] = $result['errors'];
}

// Redirect back to account page
redirect('/views/user/account.php');
?>

