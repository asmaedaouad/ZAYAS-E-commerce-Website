<?php
class ProductModel {
    private $conn;
    private $table = 'products';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get all products
    public function getProducts($limit = null, $type = null, $isNew = null) {
        // Base query
        $query = "SELECT * FROM " . $this->table;

        // Add conditions
        $conditions = [];

        if ($type) {
            $conditions[] = "type = :type";
        }

        if ($isNew !== null) {
            $conditions[] = "is_new = :is_new";
        }

        // Combine conditions
        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        // Add order and limit
        $query .= " ORDER BY created_at DESC";

        if ($limit) {
            $query .= " LIMIT :limit";
        }

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        if ($type) {
            $stmt->bindParam(':type', $type);
        }

        if ($isNew !== null) {
            $stmt->bindParam(':is_new', $isNew, PDO::PARAM_BOOL);
        }

        if ($limit) {
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        }

        // Execute query
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Get new arrivals
    public function getNewArrivals($limit = 4) {
        return $this->getProducts($limit, null, 1);
    }

    // Get product by ID
    public function getProductById($id) {
        // Query
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind parameter
        $stmt->bindParam(':id', $id);

        // Execute query
        $stmt->execute();

        return $stmt->fetch();
    }

    // Search products
    public function searchProducts($keyword) {
        // Query
        $query = "SELECT * FROM " . $this->table . "
                  WHERE name LIKE :keyword
                  OR description LIKE :keyword
                  OR type LIKE :keyword
                  ORDER BY created_at DESC";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind parameter
        $keyword = "%{$keyword}%";
        $stmt->bindParam(':keyword', $keyword);

        // Execute query
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Update product quantity
    public function updateProductQuantity($productId, $quantityChange) {
        // Query to get current quantity
        $query = "SELECT quantity FROM " . $this->table . " WHERE id = :id";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind parameter
        $stmt->bindParam(':id', $productId);

        // Execute query
        $stmt->execute();

        // Get current quantity
        $product = $stmt->fetch();
        if (!$product) {
            return false;
        }

        // Calculate new quantity
        $newQuantity = $product['quantity'] + $quantityChange;

        // Ensure quantity doesn't go below 0
        if ($newQuantity < 0) {
            $newQuantity = 0;
        }

        // Update query
        $query = "UPDATE " . $this->table . "
                  SET quantity = :quantity
                  WHERE id = :id";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':quantity', $newQuantity);
        $stmt->bindParam(':id', $productId);

        // Execute query
        return $stmt->execute();
    }
}
?>
