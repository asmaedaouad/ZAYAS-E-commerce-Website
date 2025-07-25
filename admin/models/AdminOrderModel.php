<?php
class AdminOrderModel {
    private $conn;
    private $table = 'orders';
    private $itemsTable = 'order_items';

    public function __construct($db) {
        $this->conn = $db;
    }

   
    public function getOrders() {
        return $this->getFilteredOrders();
    }

    
    public function getFilteredOrders($filters = []) {
        
        $query = "SELECT o.*, u.first_name, u.last_name, u.email
                  FROM " . $this->table . " o
                  JOIN users u ON o.user_id = u.id";

        
        $conditions = [];
        $params = [];

        
        if (isset($filters['status']) && !empty($filters['status'])) {
            $conditions[] = "o.status = :status";
            $params[':status'] = $filters['status'];
        }

       
        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

       
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

    
    public function getOrderById($id) {
       
        $query = "SELECT o.*, u.first_name, u.last_name, u.email, u.address, u.city, u.postal_code, u.phone
                  FROM " . $this->table . " o
                  JOIN users u ON o.user_id = u.id
                  WHERE o.id = :id";

       
        $stmt = $this->conn->prepare($query);

        
        $stmt->bindParam(':id', $id);

       
        $stmt->execute();

        return $stmt->fetch();
    }

    
    public function getOrderItems($orderId) {
       
        $query = "SELECT oi.*, p.name, p.image_path, p.type
                  FROM " . $this->itemsTable . " oi
                  JOIN products p ON oi.product_id = p.id
                  WHERE oi.order_id = :order_id";

        
        $stmt = $this->conn->prepare($query);

        
        $stmt->bindParam(':order_id', $orderId);

        
        $stmt->execute();

        return $stmt->fetchAll();
    }

    
    public function updateOrderStatus($id, $status) {
        
        $query = "UPDATE " . $this->table . "
                  SET status = :status
                  WHERE id = :id";

       
        $stmt = $this->conn->prepare($query);

        
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);

        
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    
    public function getOrderStatusCounts() {
        
        $query = "SELECT status, COUNT(*) as count
                  FROM " . $this->table . "
                  GROUP BY status";

        
        $stmt = $this->conn->prepare($query);

       
        $stmt->execute();

        $counts = [];
        while ($row = $stmt->fetch()) {
            $counts[$row['status']] = $row['count'];
        }

        return $counts;
    }

    // Get delivery information for an order
    public function getDeliveryInfo($orderId) {
        
        $query = "SELECT d.*,
                  dp.id as personnel_id,
                  dp.first_name as personnel_first_name,
                  dp.last_name as personnel_last_name,
                  dp.phone as personnel_phone
                  FROM delivery d
                  LEFT JOIN users dp ON d.personnel_id = dp.id AND dp.is_delivery = 1
                  WHERE d.order_id = :order_id";

        
        $stmt = $this->conn->prepare($query);

        
        $stmt->bindParam(':order_id', $orderId);

       
        $stmt->execute();

        return $stmt->fetch();
    }
}
?>
