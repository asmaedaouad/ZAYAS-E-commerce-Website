<?php
class CartModel {
    private $conn;
    private $table = 'cart';

    public function __construct($db = null) {
        $this->conn = $db;
    }

    // Add item to cart
    public function addToCart($userId, $productId, $quantity = 1) {
        // If not logged in, use session cart
        if (!$userId || !$this->conn) {
            return $this->addToSessionCart($productId, $quantity);
        }

        try {
            // Check if product already in cart
            $query = "SELECT * FROM " . $this->table . "
                      WHERE user_id = :user_id AND product_id = :product_id";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':product_id', $productId);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                // Update quantity
                $item = $stmt->fetch();
                $newQuantity = $item['quantity'] + $quantity;

                $query = "UPDATE " . $this->table . "
                          SET quantity = :quantity, updated_at = NOW()
                          WHERE id = :id";

                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':quantity', $newQuantity);
                $stmt->bindParam(':id', $item['id']);
                return $stmt->execute();
            } else {
                // Add new product
                $query = "INSERT INTO " . $this->table . "
                          SET user_id = :user_id,
                              product_id = :product_id,
                              quantity = :quantity";

                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':user_id', $userId);
                $stmt->bindParam(':product_id', $productId);
                $stmt->bindParam(':quantity', $quantity);
                return $stmt->execute();
            }
        } catch (Exception $e) {
            // Log error (in a production environment)
            // error_log('Cart addition failed: ' . $e->getMessage());
            return false;
        }
    }

    // Add item to session cart (for non-logged in users)
    private function addToSessionCart($productId, $quantity = 1) {
        $this->initSessionCart();

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

    // Initialize session cart
    private function initSessionCart() {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    // Update cart item quantity
    public function updateCartItem($userId, $productId, $quantity) {
        // If not logged in, use session cart
        if (!$userId || !$this->conn) {
            return $this->updateSessionCartItem($productId, $quantity);
        }

        if ($quantity <= 0) {
            // Remove item if quantity is 0 or negative
            return $this->removeFromCart($userId, $productId);
        }

        try {
            $query = "UPDATE " . $this->table . "
                      SET quantity = :quantity, updated_at = NOW()
                      WHERE user_id = :user_id AND product_id = :product_id";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':quantity', $quantity);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':product_id', $productId);
            return $stmt->execute();
        } catch (Exception $e) {
            return false;
        }
    }

    // Update session cart item quantity
    private function updateSessionCartItem($productId, $quantity) {
        $this->initSessionCart();

        if ($quantity <= 0) {
            // Remove item if quantity is 0 or negative
            return $this->removeFromSessionCart($productId);
        } else {
            // Update quantity
            $_SESSION['cart'][$productId] = $quantity;
        }

        return true;
    }

    // Remove item from cart
    public function removeFromCart($userId, $productId) {
        // If not logged in, use session cart
        if (!$userId || !$this->conn) {
            return $this->removeFromSessionCart($productId);
        }

        try {
            // Debug: Check if the item exists in the cart
            if (isset($_GET['debug'])) {
                $checkQuery = "SELECT * FROM " . $this->table . "
                              WHERE user_id = :user_id AND product_id = :product_id";
                $checkStmt = $this->conn->prepare($checkQuery);
                $checkStmt->bindParam(':user_id', $userId);
                $checkStmt->bindParam(':product_id', $productId);
                $checkStmt->execute();

                echo '<pre>';
                echo "Item exists in cart: " . ($checkStmt->rowCount() > 0 ? 'Yes' : 'No') . "\n";
                if ($checkStmt->rowCount() > 0) {
                    $item = $checkStmt->fetch(PDO::FETCH_ASSOC);
                    print_r($item);
                }
                echo '</pre>';
            }

            $query = "DELETE FROM " . $this->table . "
                      WHERE user_id = :user_id AND product_id = :product_id";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':product_id', $productId);
            $result = $stmt->execute();

            // Debug: Check if any rows were affected
            if (isset($_GET['debug'])) {
                echo '<pre>';
                echo "Rows affected: " . $stmt->rowCount() . "\n";
                echo '</pre>';
            }

            return $result;
        } catch (Exception $e) {
            // Debug: Log the error
            if (isset($_GET['debug'])) {
                echo '<pre>';
                echo "Error: " . $e->getMessage() . "\n";
                echo '</pre>';
            }
            return false;
        }
    }

    // Remove item from session cart
    private function removeFromSessionCart($productId) {
        $this->initSessionCart();

        if (isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
        }

        return true;
    }

    // Get cart items
    public function getCartItems($userId) {
        // If not logged in, use session cart
        if (!$userId || !$this->conn) {
            return $this->getSessionCartItems();
        }

        try {
            $query = "SELECT product_id, quantity FROM " . $this->table . "
                      WHERE user_id = :user_id";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();

            $items = [];
            while ($row = $stmt->fetch()) {
                $items[$row['product_id']] = $row['quantity'];
            }

            return $items;
        } catch (Exception $e) {
            return [];
        }
    }

    // Get session cart items
    private function getSessionCartItems() {
        $this->initSessionCart();
        return $_SESSION['cart'];
    }

    // Get cart count
    public function getCartCount($userId) {
        // If not logged in, use session cart
        if (!$userId || !$this->conn) {
            return $this->getSessionCartCount();
        }

        try {
            $query = "SELECT SUM(quantity) as count FROM " . $this->table . "
                      WHERE user_id = :user_id";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();

            $result = $stmt->fetch();
            return $result['count'] ? (int)$result['count'] : 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    // Get session cart count
    private function getSessionCartCount() {
        $this->initSessionCart();

        $count = 0;
        foreach ($_SESSION['cart'] as $quantity) {
            $count += $quantity;
        }

        return $count;
    }

    // Clear cart
    public function clearCart($userId) {
        // If not logged in, use session cart
        if (!$userId || !$this->conn) {
            return $this->clearSessionCart();
        }

        try {
            $query = "DELETE FROM " . $this->table . "
                      WHERE user_id = :user_id";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $userId);
            return $stmt->execute();
        } catch (Exception $e) {
            return false;
        }
    }

    // Clear session cart
    private function clearSessionCart() {
        $_SESSION['cart'] = [];
        return true;
    }

    // Transfer session cart to database (when user logs in)
    public function transferSessionCartToDb($userId) {
        if (!$userId || !$this->conn) {
            return false;
        }

        $this->initSessionCart();

        if (empty($_SESSION['cart'])) {
            return true;
        }

        try {
            $this->conn->beginTransaction();

            foreach ($_SESSION['cart'] as $productId => $quantity) {
                $this->addToCart($userId, $productId, $quantity);
            }

            // Clear session cart after transfer
            $this->clearSessionCart();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
}
?>
