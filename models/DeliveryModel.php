<?php
class DeliveryModel {
    private $conn;
    private $table = 'delivery';

    public function __construct($db) {
        $this->conn = $db;
    }

    
    public function createDelivery($orderId, $address, $city, $postalCode, $phone, $notes = null) {
        
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

        
        $stmt = $this->conn->prepare($query);

        // Bind parameter
        $stmt->bindParam(':order_id', $orderId);

        
        $stmt->execute();

        return $stmt->fetch();
    }

    
    public function updateDeliveryStatus($deliveryId, $status) {
        
        $query = "UPDATE " . $this->table . " 
                  SET delivery_status = :status 
                  WHERE id = :id";

        
        $stmt = $this->conn->prepare($query);

        
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $deliveryId);

        
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    
    public function updateDeliveryDate($deliveryId, $date) {
        
        $query = "UPDATE " . $this->table . " 
                  SET delivery_date = :date 
                  WHERE id = :id";

        
        $stmt = $this->conn->prepare($query);

        
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':id', $deliveryId);

        
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    
    public function getPendingDeliveries() {
        
        $query = "SELECT d.*, o.id as order_id, o.total_amount, o.created_at as order_date, 
                         u.first_name, u.last_name, u.email 
                  FROM " . $this->table . " d
                  JOIN orders o ON d.order_id = o.id
                  JOIN users u ON o.user_id = u.id
                  WHERE d.delivery_status = 'pending'
                  ORDER BY o.created_at ASC";

       
        $stmt = $this->conn->prepare($query);

        
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
?>
