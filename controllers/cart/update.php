<?php
// Include configuration
require_once '../../config/config.php';
require_once '../../config/Database.php';
require_once '../../controllers/CartController.php';

// Allow non-logged in users to update cart (will be stored in session)
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

// Check if this is a batch update (multiple products) or single product update
if (isset($_POST['product_ids']) && isset($_POST['quantities'])) {
    // Batch update (previously handled by update-all.php)
    $productIds = $_POST['product_ids'];
    $quantities = $_POST['quantities'];
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

    // Update each product in cart
    if (count($productIds) === count($quantities)) {
        for ($i = 0; $i < count($productIds); $i++) {
            $productId = (int)$productIds[$i];
            $quantity = (int)$quantities[$i];

            if ($productId > 0 && $quantity > 0) {
                // Use the CartModel to update the item
                $cartController->updateCartItemDirect($userId, $productId, $quantity);
            }
        }
    }
} else {
    // Single product update
    $cartController->updateCartItem();
}

// Get the referer URL to maintain the user's position
$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : url('/views/user/account.php');

// If the referer doesn't contain the cart hash, add it
if (strpos($referer, '#cart') === false && strpos($referer, 'account.php') !== false) {
    $referer = $referer . '#cart';
}

// Redirect back to the same page
header('Location: ' . $referer);
exit;
?>
