<?php
// Include configuration
require_once '../../config/config.php';
require_once '../../config/Database.php';
require_once '../../controllers/UserController.php';


if (!isLoggedIn() || !isAdmin()) {
    redirect('/views/auth/login.php');
}


$database = new Database();
$db = $database->getConnection();


$userController = new UserController($db);


$errors = [];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $currentPassword = isset($_POST['current_password']) ? $_POST['current_password'] : '';
    $newPassword = isset($_POST['new_password']) ? $_POST['new_password'] : '';
    $confirmPassword = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    
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

    
    if (empty($errors)) {
        
        $user = $userController->getUserById($_SESSION['user_id']);

        
        if (password_verify($currentPassword, $user['password'])) {
            if ($userController->updatePassword($_SESSION['user_id'], $newPassword)) {
                
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


redirect('/admin/profile.php');
?>

