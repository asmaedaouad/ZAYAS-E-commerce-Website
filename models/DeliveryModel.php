<?php
class DeliveryModel {
    private $conn;
    private $table = 'delivery';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create delivery record
    public function createDelivery($orderId, $address, $city, $postalCode, $phone, $notes = null) {
        // Insert query
        $query = "INSERT INTO " . $this->table . " 
                  SET order_id = :order_id, 
                      address = :address, 
                      city = :city, 
                      postal_code = :postal_code, 
                      phone = :phone, 
                      delivery_notes = :notes";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':order_id', $orderId);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':city', $city);
        $stmt->bindParam(':postal_code', $postalCode);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':notes', $notes);

        // Execute query
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }

        return false;
    }

    // Get delivery by order ID
    public function getDeliveryByOrderId($orderId) {
        // Query
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE order_id = :order_id";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind parameter
        $stmt->bindParam(':order_id', $orderId);

        // Execute query
        $stmt->execute();

        return $stmt->fetch();
    }

    // Update delivery status
    public function updateDeliveryStatus($deliveryId, $status) {
        // Update query
        $query = "UPDATE " . $this->table . " 
                  SET delivery_status = :status 
                  WHERE id = :id";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $deliveryId);

        // Execute query
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Update delivery date
    public function updateDeliveryDate($deliveryId, $date) {
        // Update query
        $query = "UPDATE " . $this->table . " 
                  SET delivery_date = :date 
                  WHERE id = :id";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':id', $deliveryId);

        // Execute query
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Get all pending deliveries
    public function getPendingDeliveries() {
        // Query
        $query = "SELECT d.*, o.id as order_id, o.total_amount, o.created_at as order_date, 
                         u.first_name, u.last_name, u.email 
                  FROM " . $this->table . " d
                  JOIN orders o ON d.order_id = o.id
                  JOIN users u ON o.user_id = u.id
                  WHERE d.delivery_status = 'pending'
                  ORDER BY o.created_at ASC";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Execute query
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
?>
