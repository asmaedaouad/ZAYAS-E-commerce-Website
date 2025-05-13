<?php
// Include configuration
require_once '../../config/config.php';
require_once '../../config/Database.php';
require_once '../../controllers/CartController.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Create cart controller
$cartController = new CartController($db);

// Handle remove from cart
$cartController->removeFromCart();

// Redirect back to cart page (this should not be reached as removeFromCart redirects)
redirect('/views/user/cart.php');
?>
