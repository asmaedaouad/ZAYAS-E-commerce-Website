<?php
class AdminDeliveryModel {
    private $conn;
    private $table = 'delivery';
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Get all deliveries with optional filtering
    public function getDeliveries($filters = []) {
        // Base query
        $query = "SELECT d.*, o.id as order_id, o.total_amount, o.created_at as order_date, 
                         u.first_name, u.last_name, u.email 
                  FROM " . $this->table . " d
                  JOIN orders o ON d.order_id = o.id
                  JOIN users u ON o.user_id = u.id";
        
        // Add filters
        $conditions = [];
        $params = [];
        
        // Filter by status
        if (isset($filters['status']) && !empty($filters['status'])) {
            $conditions[] = "d.delivery_status = :status";
            $params[':status'] = $filters['status'];
        }
        
        // Filter by date range
        if (isset($filters['date_from']) && !empty($filters['date_from'])) {
            $conditions[] = "DATE(o.created_at) >= :date_from";
            $params[':date_from'] = $filters['date_from'];
        }
        
        if (isset($filters['date_to']) && !empty($filters['date_to'])) {
            $conditions[] = "DATE(o.created_at) <= :date_to";
            $params[':date_to'] = $filters['date_to'];
        }
        
        // Search by customer name or email
        if (isset($filters['search']) && !empty($filters['search'])) {
            $conditions[] = "(u.first_name LIKE :search OR u.last_name LIKE :search OR u.email LIKE :search OR CONCAT(u.first_name, ' ', u.last_name) LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        
        // Add conditions to query
        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }
        
        // Add order by
        $query .= " ORDER BY o.created_at DESC";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        // Bind parameters
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        // Execute query
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    // Get delivery by ID
    public function getDeliveryById($id) {
        // Query
        $query = "SELECT d.*, o.id as order_id, o.total_amount, o.status as order_status, o.created_at as order_date, 
                         u.first_name, u.last_name, u.email, u.phone as user_phone 
                  FROM " . $this->table . " d
                  JOIN orders o ON d.order_id = o.id
                  JOIN users u ON o.user_id = u.id
                  WHERE d.id = :id";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        // Bind parameter
        $stmt->bindParam(':id', $id);
        
        // Execute query
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    // Get delivery by order ID
    public function getDeliveryByOrderId($orderId) {
        // Query
        $query = "SELECT * FROM " . $this->table . " WHERE order_id = :order_id";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        // Bind parameter
        $stmt->bindParam(':order_id', $orderId);
        
        // Execute query
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    // Update delivery status
    public function updateDeliveryStatus($id, $status) {
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
            return true;
        }
        
        return false;
    }
    
    // Update order status based on delivery status
    public function updateOrderStatus($orderId, $status) {
        // Update query
        $query = "UPDATE orders 
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
    
    // Get delivery status counts
    public function getDeliveryStatusCounts() {
        // Query
        $query = "SELECT delivery_status, COUNT(*) as count 
                  FROM " . $this->table . " 
                  GROUP BY delivery_status";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        // Execute query
        $stmt->execute();
        
        $counts = [];
        while ($row = $stmt->fetch()) {
            $counts[$row['delivery_status']] = $row['count'];
        }
        
        return $counts;
    }
    
    // Get delivery personnel (users with is_delivery = 1)
    public function getDeliveryPersonnel() {
        // Query
        $query = "SELECT id, first_name, last_name, email, phone 
                  FROM users 
                  WHERE is_delivery = 1 
                  ORDER BY first_name, last_name";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        // Execute query
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    // Assign delivery to personnel
    public function assignDelivery($deliveryId, $personnelId) {
        // Update query
        $query = "UPDATE " . $this->table . " 
                  SET personnel_id = :personnel_id, 
                      delivery_status = 'assigned' 
                  WHERE id = :id";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        // Bind parameters
        $stmt->bindParam(':personnel_id', $personnelId);
        $stmt->bindParam(':id', $deliveryId);
        
        // Execute query
        if ($stmt->execute()) {
            // Get order ID
            $query = "SELECT order_id FROM " . $this->table . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $deliveryId);
            $stmt->execute();
            $result = $stmt->fetch();
            
            if ($result) {
                // Update order status
                $this->updateOrderStatus($result['order_id'], 'assigned');
            }
            
            return true;
        }
        
        return false;
    }
}
?>
