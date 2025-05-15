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

// Handle remove from wishlist
$wishlistController->removeFromWishlist();

// Redirect back to account page wishlist tab (this should not be reached as removeFromWishlist redirects)
redirect('/views/user/account.php#wishlist');
?>

