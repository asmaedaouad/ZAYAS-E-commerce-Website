<?php
// Include configuration
require_once '../../config/config.php';
require_once '../../config/Database.php';
require_once '../../controllers/UserController.php';

// Redirect if not logged in or not admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('/views/auth/login.php');
}

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Create user controller
$userController = new UserController($db);

// Initialize errors array
$errors = [];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $currentPassword = isset($_POST['current_password']) ? $_POST['current_password'] : '';
    $newPassword = isset($_POST['new_password']) ? $_POST['new_password'] : '';
    $confirmPassword = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    // Validate input
    if (empty($currentPassword)) {
        $errors[] = 'Current password is required';
    }

    if (empty($newPassword)) {
        $errors[] = 'New password is required';
    } elseif (strlen($newPassword) < 6) {
        $errors[] = 'New password must be at least 6 characters';
    }

    if ($newPassword !== $confirmPassword) {
        $errors[] = 'Passwords do not match';
    }

    // If no errors, verify current password and update
    if (empty($errors)) {
        // Get user data
        $user = $userController->getUserById($_SESSION['user_id']);

        // Verify current password
        if (password_verify($currentPassword, $user['password'])) {
            if ($userController->updatePassword($_SESSION['user_id'], $newPassword)) {
                // Set success message
                $_SESSION['password_success'] = 'Password updated successfully';
            } else {
                $errors[] = 'Failed to update password';
                $_SESSION['password_errors'] = $errors;
            }
        } else {
            $errors[] = 'Current password is incorrect';
            $_SESSION['password_errors'] = $errors;
        }
    } else {
        // Set error messages
        $_SESSION['password_errors'] = $errors;
    }
}

// Redirect back to profile page
redirect('/admin/profile.php');
?>

