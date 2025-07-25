<?php

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
    // Get form data
    $firstName = isset($_POST['first_name']) ? sanitize($_POST['first_name']) : '';
    $lastName = isset($_POST['last_name']) ? sanitize($_POST['last_name']) : '';
    $address = isset($_POST['address']) ? sanitize($_POST['address']) : '';
    $city = isset($_POST['city']) ? sanitize($_POST['city']) : '';
    $postalCode = isset($_POST['postal_code']) ? sanitize($_POST['postal_code']) : '';
    $phone = isset($_POST['phone']) ? sanitize($_POST['phone']) : '';

    
    if (empty($firstName)) {
        $errors[] = 'First name is required';
    } elseif (!preg_match('/^[A-Za-zÀ-ÖØ-öø-ÿ\'\-\s]{2,50}$/', $firstName)) {
        $errors[] = 'First name must be 2-50 characters and contain only letters, spaces, apostrophes, and hyphens';
    }

    if (empty($lastName)) {
        $errors[] = 'Last name is required';
    } elseif (!preg_match('/^[A-Za-zÀ-ÖØ-öø-ÿ\'\-\s]{2,50}$/', $lastName)) {
        $errors[] = 'Last name must be 2-50 characters and contain only letters, spaces, apostrophes, and hyphens';
    }

    if (!empty($city) && !preg_match('/^[A-Za-zÀ-ÖØ-öø-ÿ\s\'\-]{2,50}$/', $city)) {
        $errors[] = 'City must contain only letters, spaces, apostrophes, and hyphens';
    }

    
    if (empty($errors)) {
        if ($userController->updateProfile($_SESSION['user_id'], $firstName, $lastName, $address, $city, $postalCode, $phone)) {
            // Update session variables
            $_SESSION['first_name'] = $firstName;
            $_SESSION['last_name'] = $lastName;

            // Set success message
            $_SESSION['profile_success'] = 'Profile updated successfully';
        } else {
            $errors[] = 'Failed to update profile';
            $_SESSION['profile_errors'] = $errors;
        }
    } else {
        
        $_SESSION['profile_errors'] = $errors;
    }
}


redirect('/admin/profile.php');
?>

