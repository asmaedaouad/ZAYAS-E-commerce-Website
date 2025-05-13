<?php
require_once __DIR__ . '/../models/CartModel.php';
require_once __DIR__ . '/../models/ProductModel.php';

class CartController {
    private $cartModel;
    private $productModel;
    private $db;

    public function __construct($db = null) {
        $this->db = $db;
        $this->cartModel = new CartModel($db);

        if ($db) {
            $this->productModel = new ProductModel($db);
        }
    }

    // Get current user ID
    private function getCurrentUserId() {
        return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
    }

    // Add product to cart
    public function addToCart() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
            $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
            $userId = $this->getCurrentUserId();

            if ($productId > 0 && $quantity > 0) {
                $this->cartModel->addToCart($userId, $productId, $quantity);

                // Redirect back to previous page
                $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : url('/index.php');
                header('Location: ' . $referer);
                exit;
            }
        }

        // Invalid request
        redirect('/index.php');
    }

    // Update cart item
    public function updateCartItem() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
            $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
            $userId = $this->getCurrentUserId();

            if ($productId > 0) {
                $this->cartModel->updateCartItem($userId, $productId, $quantity);
            }

            // Redirect back to account page cart tab
            redirect('/views/user/account.php#cart');
        }

        // Invalid request
        redirect('/views/user/account.php#cart');
    }

    // Update cart item directly (for batch updates)
    public function updateCartItemDirect($userId, $productId, $quantity) {
        if ($productId > 0) {
            return $this->cartModel->updateCartItem($userId, $productId, $quantity);
        }
        return false;
    }

    // Remove item from cart
    public function removeFromCart() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
            $userId = $this->getCurrentUserId();

            // Debug: Log the product ID and user ID
            if (isset($_GET['debug'])) {
                echo '<pre>';
                echo "Product ID: $productId\n";
                echo "User ID: $userId\n";
                echo '</pre>';
            }

            if ($productId > 0) {
                $result = $this->cartModel->removeFromCart($userId, $productId);

                // Debug: Log the result of the removal
                if (isset($_GET['debug'])) {
                    echo '<pre>';
                    echo "Removal result: " . ($result ? 'Success' : 'Failed') . "\n";
                    echo '</pre>';
                    exit;
                }
            }

            // Redirect back to account page cart tab
            redirect('/views/user/account.php#cart');
        }

        // Invalid request
        redirect('/views/user/account.php#cart');
    }

    // Get cart items with product details
    public function getCartWithProducts() {
        if (!$this->productModel) {
            return [
                'items' => [],
                'total_price' => 0,
                'item_count' => 0
            ];
        }

        $userId = $this->getCurrentUserId();
        $cartItems = $this->cartModel->getCartItems($userId);
        $cartWithProducts = [];
        $totalPrice = 0;

        foreach ($cartItems as $productId => $quantity) {
            $product = $this->productModel->getProductById($productId);

            if ($product) {
                $itemTotal = $product['price'] * $quantity;
                $totalPrice += $itemTotal;

                $cartWithProducts[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'total' => $itemTotal
                ];
            }
        }

        return [
            'items' => $cartWithProducts,
            'total_price' => $totalPrice,
            'item_count' => count($cartWithProducts)
        ];
    }

    // Get cart count
    public function getCartCount() {
        $userId = $this->getCurrentUserId();
        return $this->cartModel->getCartCount($userId);
    }

    // Clear cart
    public function clearCart() {
        $userId = $this->getCurrentUserId();
        $this->cartModel->clearCart($userId);
        return true;
    }

    // Display cart page
    public function viewCart() {
        return $this->getCartWithProducts();
    }

    // Transfer session cart to database when user logs in
    public function transferCartOnLogin($userId) {
        if ($userId && $this->db) {
            return $this->cartModel->transferSessionCartToDb($userId);
        }
        return false;
    }
}
?>
