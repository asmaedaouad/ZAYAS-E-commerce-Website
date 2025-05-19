<?php
// Include configuration
require_once '../../config/config.php';
require_once '../../config/Database.php';
require_once '../../controllers/WishlistController.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    redirect('/views/auth/login.php');
}

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Create wishlist controller
$wishlistController = new WishlistController($db);

// Save scroll position to session if provided
if (isset($_POST['scroll_position'])) {
    $_SESSION['scroll_position'] = (int)$_POST['scroll_position'];
}

// Handle remove from wishlist
$wishlistController->removeFromWishlist();

// Redirect back to previous page (this should not be reached as removeFromWishlist redirects)
$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : url('/index.php');
header('Location: ' . $referer);
exit;
?>

