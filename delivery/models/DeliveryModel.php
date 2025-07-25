<?php
class DeliveryModel {
    private $conn;
    private $table = 'delivery';

    public function __construct($db) {
        $this->conn = $db;
    }

    
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

        
        $stmt = $this->conn->prepare($query);

        
        $stmt->bindParam(':personnel_id', $personnelId);

        
        $stmt->execute();

        return $stmt->fetchAll();
    }

    
    public function getDeliveryById($id, $personnelId) {
        
        $query = "SELECT d.*, o.id as order_id, o.total_amount, o.status as order_status, o.created_at as order_date,
                         u.first_name, u.last_name, u.email, u.phone as user_phone,
                         u.address as user_address, u.city as user_city, u.postal_code as user_postal_code
                  FROM " . $this->table . " d
                  JOIN orders o ON d.order_id = o.id
                  JOIN users u ON o.user_id = u.id
                  WHERE d.id = :id AND d.personnel_id = :personnel_id";

        
        $stmt = $this->conn->prepare($query);

        
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':personnel_id', $personnelId);

       
        $stmt->execute();

        return $stmt->fetch();
    }

    
    public function getOrderItems($orderId) {
        
        $query = "SELECT oi.*, p.name as product_name, p.image_path
                  FROM order_items oi
                  JOIN products p ON oi.product_id = p.id
                  WHERE oi.order_id = :order_id";

        
        $stmt = $this->conn->prepare($query);

        
        $stmt->bindParam(':order_id', $orderId);

        
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Update delivery status
    public function updateDeliveryStatus($id, $personnelId, $status) {
        // First check if this delivery belongs to this personnel
        $query = "SELECT d.*, o.status as order_status
                  FROM " . $this->table . " d
                  JOIN orders o ON d.order_id = o.id
                  WHERE d.id = :id AND d.personnel_id = :personnel_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':personnel_id', $personnelId);
        $stmt->execute();

        $delivery = $stmt->fetch();
        if (!$delivery) {
            return false; 
        }

        
        $currentStatus = $delivery['delivery_status'];
        $orderId = $delivery['order_id'];

        
        $query = "UPDATE " . $this->table . "
                  SET delivery_status = :status
                  WHERE id = :id";

        
        $stmt = $this->conn->prepare($query);

        
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);

        
        if ($stmt->execute()) {
            
            $this->updateOrderStatus($id, $status);

            
            if ($status === 'delivered' && $currentStatus !== 'delivered') {
                
                $this->updateProductQuantities($orderId, -1); 
            } else if ($status === 'returned' && $currentStatus === 'delivered') {
                
                $this->updateProductQuantities($orderId, 1); 
            }

            return true;
        }

        return false;
    }

    
    private function updateOrderStatus($deliveryId, $deliveryStatus) {
        
        $query = "SELECT order_id FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $deliveryId);
        $stmt->execute();
        $result = $stmt->fetch();

        if (!$result) {
            return false;
        }

        $orderId = $result['order_id'];
        $orderStatus = $deliveryStatus; 

        
        $query = "UPDATE orders SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $orderStatus);
        $stmt->bindParam(':id', $orderId);

        return $stmt->execute();
    }

    
    public function getDeliveryStatusCounts($personnelId) {
        
        $query = "SELECT delivery_status, COUNT(*) as count
                  FROM " . $this->table . "
                  WHERE personnel_id = :personnel_id
                  GROUP BY delivery_status";

        
        $stmt = $this->conn->prepare($query);

        
        $stmt->bindParam(':personnel_id', $personnelId);

        
        $stmt->execute();

        $counts = [];
        while ($row = $stmt->fetch()) {
            $counts[$row['delivery_status']] = $row['count'];
        }

        return $counts;
    }

    
    public function getTotalPendingOrdersCount() {
        
        $query = "SELECT COUNT(*) as count
                  FROM " . $this->table . "
                  WHERE delivery_status = 'pending'";

        
        $stmt = $this->conn->prepare($query);

        
        $stmt->execute();

        $result = $stmt->fetch();
        return $result ? (int)$result['count'] : 0;
    }

    
    private function updateProductQuantities($orderId, $multiplier) {
        
        require_once __DIR__ . '/../../models/ProductModel.php';

        
        $productModel = new ProductModel($this->conn);

        
        $orderItems = $this->getOrderItems($orderId);

        
        foreach ($orderItems as $item) {
            $productId = $item['product_id'];
            $quantity = $item['quantity'];

            
            $quantityChange = $quantity * $multiplier;

            
            $productModel->updateProductQuantity($productId, $quantityChange);
        }

        return true;
    }
}
?>
