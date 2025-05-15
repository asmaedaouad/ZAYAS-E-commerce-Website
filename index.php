<?php
// Include configuration
require_once 'config/config.php';

// Check if user is delivery personnel and redirect to logout
if (isLoggedIn() && isDelivery()) {
    // Logout delivery personnel who try to access the store
    redirect('/views/auth/logout.php');
}

// Define the current directory as the base directory
define('BASE_DIR', __DIR__);

// Include the home page (not redirect)
include_once 'views/home/home.php';
?>
