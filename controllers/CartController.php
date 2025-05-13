<?php
require_once __DIR__ . '/../models/CartModel.php';
require_once __DIR__ . '/../models/ProductModel.php';

class CartController {
    private $cartModel;
    private $productModel;

    public function __construct($db = null) {
        $this->cartModel = new CartModel();

        if ($db) {
            $this->productModel = new ProductModel($db);
        }
    }

    // Add product to cart
    public function addToCart() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
            $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

            if ($productId > 0 && $quantity > 0) {
                $this->cartModel->addToCart($productId, $quantity);

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

            if ($productId > 0) {
                $this->cartModel->updateCartItem($productId, $quantity);
            }

            // Redirect back to cart
            redirect('/views/user/cart.php');
        }

        // Invalid request
        redirect('/views/user/cart.php');
    }

    // Remove item from cart
    public function removeFromCart() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

            if ($productId > 0) {
                $this->cartModel->removeFromCart($productId);
            }

            // Redirect back to cart
            redirect('/views/user/cart.php');
        }

        // Invalid request
        redirect('/views/user/cart.php');
    }

    // Get cart items with product details
    public function getCartWithProducts() {
        if (!$this->productModel) {
            return [];
        }

        $cartItems = $this->cartModel->getCartItems();
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
        return $this->cartModel->getCartCount();
    }

    // Clear cart
    public function clearCart() {
        $this->cartModel->clearCart();
        return true;
    }

    // Display cart page
    public function viewCart() {
        return $this->getCartWithProducts();
    }
}
?>
