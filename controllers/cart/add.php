<?php
// Include configuration
require_once '../../config/config.php';
require_once '../../config/Database.php';
require_once '../../controllers/CartController.php';

// Allow non-logged in users to add to cart (will be stored in session)
// No redirect needed here

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Create cart controller
$cartController = new CartController($db);

// Save scroll position to session if provided
if (isset($_POST['scroll_position'])) {
    $_SESSION['scroll_position'] = (int)$_POST['scroll_position'];
}

// Handle add to cart
$cartController->addToCart();

// Redirect back to previous page (this should not be reached as addToCart redirects)
$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : url('/index.php');
header('Location: ' . $referer);
exit;
?>
