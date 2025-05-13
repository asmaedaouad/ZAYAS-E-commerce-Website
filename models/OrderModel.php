<?php
class OrderModel {
    private $conn;
    private $table = 'orders';
    private $itemsTable = 'order_items';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create new order
    public function createOrder($userId, $items, $totalAmount) {
        try {
            // Start transaction
            $this->conn->beginTransaction();

            // Insert order
            $query = "INSERT INTO " . $this->table . "
                      SET user_id = :user_id,
                          total_amount = :total_amount";

            // Prepare statement
            $stmt = $this->conn->prepare($query);

            // Bind parameters
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':total_amount', $totalAmount);

            // Execute query
            $stmt->execute();

            // Get order ID
            $orderId = $this->conn->lastInsertId();

            // Insert order items
            foreach ($items as $productId => $item) {
                $query = "INSERT INTO " . $this->itemsTable . "
                          SET order_id = :order_id,
                              product_id = :product_id,
                              quantity = :quantity,
                              price = :price";

                // Prepare statement
                $stmt = $this->conn->prepare($query);

                // Bind parameters
                $stmt->bindParam(':order_id', $orderId);
                $stmt->bindParam(':product_id', $productId);
                $stmt->bindParam(':quantity', $item['quantity']);
                $stmt->bindParam(':price', $item['price']);

                // Execute query
                $stmt->execute();

                // Update product quantity (optional)
                // $this->updateProductQuantity($productId, $item['quantity']);
            }

            // Commit transaction
            $this->conn->commit();

            return $orderId;
        } catch (Exception $e) {
            // Rollback transaction
            $this->conn->rollBack();

            // Log error (in a production environment)
            // error_log('Order creation failed: ' . $e->getMessage());

            return false;
        }
    }

    // Get user's orders
    public function getUserOrders($userId) {
        // Query
        $query = "SELECT * FROM " . $this->table . "
                  WHERE user_id = :user_id
                  ORDER BY created_at DESC";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind parameter
        $stmt->bindParam(':user_id', $userId);

        // Execute query
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Get order by ID
    public function getOrderById($orderId) {
        // Query
        $query = "SELECT * FROM " . $this->table . "
                  WHERE id = :id";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind parameter
        $stmt->bindParam(':id', $orderId);

        // Execute query
        $stmt->execute();

        return $stmt->fetch();
    }

    // Get order items
    public function getOrderItems($orderId) {
        // Query
        $query = "SELECT oi.*, p.name, p.image_path
                  FROM " . $this->itemsTable . " oi
                  JOIN products p ON oi.product_id = p.id
                  WHERE oi.order_id = :order_id";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind parameter
        $stmt->bindParam(':order_id', $orderId);

        // Execute query
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Update order status
    public function updateOrderStatus($orderId, $status) {
        // Update query
        $query = "UPDATE " . $this->table . "
                  SET status = :status
                  WHERE id = :id";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $orderId);

        // Execute query
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}
?>
