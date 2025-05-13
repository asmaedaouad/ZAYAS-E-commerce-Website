<?php
class CartModel {
    // Cart is stored in session
    
    // Initialize cart
    public function initCart() {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }
    
    // Add item to cart
    public function addToCart($productId, $quantity = 1) {
        $this->initCart();
        
        // Check if product already in cart
        if (isset($_SESSION['cart'][$productId])) {
            // Update quantity
            $_SESSION['cart'][$productId] += $quantity;
        } else {
            // Add new product
            $_SESSION['cart'][$productId] = $quantity;
        }
        
        return true;
    }
    
    // Update cart item quantity
    public function updateCartItem($productId, $quantity) {
        $this->initCart();
        
        if ($quantity <= 0) {
            // Remove item if quantity is 0 or negative
            $this->removeFromCart($productId);
        } else {
            // Update quantity
            $_SESSION['cart'][$productId] = $quantity;
        }
        
        return true;
    }
    
    // Remove item from cart
    public function removeFromCart($productId) {
        $this->initCart();
        
        if (isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
        }
        
        return true;
    }
    
    // Get cart items
    public function getCartItems() {
        $this->initCart();
        return $_SESSION['cart'];
    }
    
    // Get cart count
    public function getCartCount() {
        $this->initCart();
        
        $count = 0;
        foreach ($_SESSION['cart'] as $quantity) {
            $count += $quantity;
        }
        
        return $count;
    }
    
    // Clear cart
    public function clearCart() {
        $_SESSION['cart'] = [];
        return true;
    }
}
?>
