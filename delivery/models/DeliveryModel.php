<?php
class DeliveryModel {
    private $conn;
    private $table = 'delivery';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get deliveries assigned to a specific delivery person
    public function getAssignedDeliveries($personnelId) {
        // Query
        $query = "SELECT d.*, o.id as order_id, o.total_amount, o.created_at as order_date, 
                         u.first_name, u.last_name, u.email, u.phone as user_phone,
                         u.address as user_address, u.city as user_city, u.postal_code as user_postal_code
                  FROM " . $this->table . " d
                  JOIN orders o ON d.order_id = o.id
                  JOIN users u ON o.user_id = u.id
                  WHERE d.personnel_id = :personnel_id
                  ORDER BY d.delivery_status ASC, o.created_at ASC";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind parameter
        $stmt->bindParam(':personnel_id', $personnelId);

        // Execute query
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Get delivery by ID
    public function getDeliveryById($id, $personnelId) {
        // Query
        $query = "SELECT d.*, o.id as order_id, o.total_amount, o.status as order_status, o.created_at as order_date, 
                         u.first_name, u.last_name, u.email, u.phone as user_phone,
                         u.address as user_address, u.city as user_city, u.postal_code as user_postal_code
                  FROM " . $this->table . " d
                  JOIN orders o ON d.order_id = o.id
                  JOIN users u ON o.user_id = u.id
                  WHERE d.id = :id AND d.personnel_id = :personnel_id";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':personnel_id', $personnelId);

        // Execute query
        $stmt->execute();

        return $stmt->fetch();
    }

    // Get order items for a delivery
    public function getOrderItems($orderId) {
        // Query
        $query = "SELECT oi.*, p.name as product_name, p.image_path
                  FROM order_items oi
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

    // Update delivery status
    public function updateDeliveryStatus($id, $personnelId, $status) {
        // First check if this delivery belongs to this personnel
        $query = "SELECT id FROM " . $this->table . " 
                  WHERE id = :id AND personnel_id = :personnel_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':personnel_id', $personnelId);
        $stmt->execute();

        if (!$stmt->fetch()) {
            return false; // Not authorized
        }

        // Update query
        $query = "UPDATE " . $this->table . " 
                  SET delivery_status = :status 
                  WHERE id = :id";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);

        // Execute query
        if ($stmt->execute()) {
            // Also update the order status
            $this->updateOrderStatus($id, $status);
            return true;
        }

        return false;
    }

    // Update order status based on delivery status
    private function updateOrderStatus($deliveryId, $deliveryStatus) {
        // Get order ID
        $query = "SELECT order_id FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $deliveryId);
        $stmt->execute();
        $result = $stmt->fetch();

        if (!$result) {
            return false;
        }

        $orderId = $result['order_id'];
        $orderStatus = $deliveryStatus; // In most cases, they match

        // Update order status
        $query = "UPDATE orders SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $orderStatus);
        $stmt->bindParam(':id', $orderId);
        
        return $stmt->execute();
    }

    // Get delivery status counts for a delivery person
    public function getDeliveryStatusCounts($personnelId) {
        // Query
        $query = "SELECT delivery_status, COUNT(*) as count 
                  FROM " . $this->table . " 
                  WHERE personnel_id = :personnel_id
                  GROUP BY delivery_status";

        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        // Bind parameter
        $stmt->bindParam(':personnel_id', $personnelId);

        // Execute query
        $stmt->execute();

        $counts = [];
        while ($row = $stmt->fetch()) {
            $counts[$row['delivery_status']] = $row['count'];
        }

        return $counts;
    }
}
?>
