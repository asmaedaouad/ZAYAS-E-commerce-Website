<?php
require_once __DIR__ . '/../models/WishlistModel.php';

class WishlistController {
    private $wishlistModel;

    public function __construct($db = null) {
        if ($db) {
            $this->wishlistModel = new WishlistModel($db);
        }
    }

    // Add product to wishlist
    public function addToWishlist() {
        if (!isLoggedIn()) {
            redirect('/views/auth/login.php');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

            if ($productId > 0) {
                $this->wishlistModel->addToWishlist($_SESSION['user_id'], $productId);

                // Redirect back to previous page
                $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : url('/index.php');
                header('Location: ' . $referer);
                exit;
            }
        }

        // Invalid request
        redirect('/index.php');
    }

    // Remove product from wishlist
    public function removeFromWishlist() {
        if (!isLoggedIn()) {
            redirect('/views/auth/login.php');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

            if ($productId > 0) {
                $this->wishlistModel->removeFromWishlist($_SESSION['user_id'], $productId);

                // Redirect back to previous page
                $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : url('/index.php');
                header('Location: ' . $referer);
                exit;
            }
        }

        // Invalid request
        redirect('/index.php');
    }

    // Get user's wishlist
    public function getWishlist() {
        if (!isLoggedIn()) {
            return [];
        }

        return $this->wishlistModel->getWishlist($_SESSION['user_id']);
    }

    // Check if product is in wishlist
    public function isInWishlist($productId) {
        if (!isLoggedIn()) {
            return false;
        }

        return $this->wishlistModel->isInWishlist($_SESSION['user_id'], $productId);
    }

    // Get wishlist count
    public function getWishlistCount() {
        if (!isLoggedIn()) {
            return 0;
        }

        return $this->wishlistModel->getWishlistCount($_SESSION['user_id']);
    }

    // Display wishlist page
    public function viewWishlist() {
        if (!isLoggedIn()) {
            redirect('/views/auth/login.php');
        }

        return [
            'wishlist' => $this->getWishlist()
        ];
    }
}
?>

