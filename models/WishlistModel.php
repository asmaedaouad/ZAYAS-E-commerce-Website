<?php
class WishlistModel {
    private $conn;
    private $table = 'wishlist';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Add product to wishlist
    public function addToWishlist($userId, $productId) {
        // Check if already in wishlist
        if ($this->isInWishlist($userId, $productId)) {
            return true;
        }

        // Insert query
        $query = "INSERT INTO " . $this->table . " 
                  SET user_id = :user_id, 
                      product_id = :product_id";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':product_id', $productId);

        // Execute query
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Remove product from wishlist
    public function removeFromWishlist($userId, $productId) {
        // Delete query
        $query = "DELETE FROM " . $this->table . " 
                  WHERE user_id = :user_id 
                  AND product_id = :product_id";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':product_id', $productId);

        // Execute query
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Check if product is in wishlist
    public function isInWishlist($userId, $productId) {
        // Query
        $query = "SELECT id FROM " . $this->table . " 
                  WHERE user_id = :user_id 
                  AND product_id = :product_id";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':product_id', $productId);

        // Execute query
        $stmt->execute();

        // Get row count
        $num = $stmt->rowCount();

        // If product is in wishlist
        if ($num > 0) {
            return true;
        }

        return false;
    }

    // Get user's wishlist
    public function getWishlist($userId) {
        // Query
        $query = "SELECT w.id, w.product_id, p.name, p.price, p.image_path, p.type 
                  FROM " . $this->table . " w
                  JOIN products p ON w.product_id = p.id
                  WHERE w.user_id = :user_id
                  ORDER BY w.created_at DESC";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind parameter
        $stmt->bindParam(':user_id', $userId);

        // Execute query
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Get wishlist count
    public function getWishlistCount($userId) {
        // Query
        $query = "SELECT COUNT(*) as count FROM " . $this->table . " 
                  WHERE user_id = :user_id";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind parameter
        $stmt->bindParam(':user_id', $userId);

        // Execute query
        $stmt->execute();

        // Get result
        $row = $stmt->fetch();

        return $row['count'];
    }
}
?>
