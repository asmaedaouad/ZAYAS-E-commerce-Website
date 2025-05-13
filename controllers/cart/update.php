<?php
// Include configuration
require_once '../../config/config.php';
require_once '../../config/Database.php';
require_once '../../controllers/CartController.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    redirect('/views/auth/login.php');
}

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Create cart controller
$cartController = new CartController($db);

// Handle update cart item
$cartController->updateCartItem();

// Redirect back to cart page (this should not be reached as updateCartItem redirects)
redirect('/views/user/cart.php');
?>
