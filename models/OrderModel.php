<?php
class OrderModel {
    private $conn;
    private $table = 'orders';
    private $itemsTable = 'order_items';

    public function __construct($db) {
        $this->conn = $db;
    }

    
    public function createOrder($userId, $items, $totalAmount) {
        try {
            
            $this->conn->beginTransaction();

            
            $query = "INSERT INTO " . $this->table . "
                      SET user_id = :user_id,
                          total_amount = :total_amount";

            
            $stmt = $this->conn->prepare($query);

            // Bind parameters
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':total_amount', $totalAmount);

            
            $stmt->execute();

            
            $orderId = $this->conn->lastInsertId();

           
            foreach ($items as $productId => $item) {
                $query = "INSERT INTO " . $this->itemsTable . "
                          SET order_id = :order_id,
                              product_id = :product_id,
                              quantity = :quantity,
                              price = :price";

                
                $stmt = $this->conn->prepare($query);

                // Bind parameters
                $stmt->bindParam(':order_id', $orderId);
                $stmt->bindParam(':product_id', $productId);
                $stmt->bindParam(':quantity', $item['quantity']);
                $stmt->bindParam(':price', $item['price']);

                
                $stmt->execute();

                
                
            }

            
            $this->conn->commit();

            return $orderId;
        } catch (Exception $e) {
            
            $this->conn->rollBack();

            

            return false;
        }
    }

    
    public function getUserOrders($userId) {
        
        $query = "SELECT * FROM " . $this->table . "
                  WHERE user_id = :user_id
                  ORDER BY created_at DESC";

        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':user_id', $userId);
        
        $stmt->execute();

        return $stmt->fetchAll();
    }

    
    public function getOrderById($orderId) {
        // Query
        $query = "SELECT * FROM " . $this->table . "
                  WHERE id = :id";

        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id', $orderId);

        $stmt->execute();

        return $stmt->fetch();
    }

    public function getOrderItems($orderId) {
        
        $query = "SELECT oi.*, p.name, p.image_path
                  FROM " . $this->itemsTable . " oi
                  JOIN products p ON oi.product_id = p.id
                  WHERE oi.order_id = :order_id";

        
        $stmt = $this->conn->prepare($query);

        
        $stmt->bindParam(':order_id', $orderId);

        
        $stmt->execute();

        return $stmt->fetchAll();
    }

    
    public function updateOrderStatus($orderId, $status) {
       
        $query = "UPDATE " . $this->table . "
                  SET status = :status
                  WHERE id = :id";

        
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
