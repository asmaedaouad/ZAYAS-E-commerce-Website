<?php
class AdminDeliveryModel {
    private $conn;
    private $table = 'delivery';
    private $usersTable = 'users';

    public function __construct($db) {
        $this->conn = $db;
    }

    
    public function getDeliveries($filters = []) {
       
        $query = "SELECT d.*, o.id as order_id, o.total_amount, o.created_at as order_date,
                         u.first_name, u.last_name, u.email
                  FROM " . $this->table . " d
                  JOIN orders o ON d.order_id = o.id
                  JOIN users u ON o.user_id = u.id";

        
        $conditions = [];
        $params = [];

        
        if (isset($filters['status']) && !empty($filters['status'])) {
            $conditions[] = "d.delivery_status = :status";
            $params[':status'] = $filters['status'];
        }

        
        if (isset($filters['date_from']) && !empty($filters['date_from'])) {
            $conditions[] = "DATE(o.created_at) >= :date_from";
            $params[':date_from'] = $filters['date_from'];
        }

        if (isset($filters['date_to']) && !empty($filters['date_to'])) {
            $conditions[] = "DATE(o.created_at) <= :date_to";
            $params[':date_to'] = $filters['date_to'];
        }

        
        if (isset($filters['search']) && !empty($filters['search'])) {
            $conditions[] = "(u.first_name LIKE :search OR u.last_name LIKE :search OR u.email LIKE :search OR CONCAT(u.first_name, ' ', u.last_name) LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        
        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        
        $query .= " ORDER BY o.created_at DESC";

        
        $stmt = $this->conn->prepare($query);

        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        
        $stmt->execute();

        return $stmt->fetchAll();
    }

    
    public function getDeliveryById($id) {
        
        $query = "SELECT d.*, o.id as order_id, o.total_amount, o.status as order_status, o.created_at as order_date,
                         u.first_name, u.last_name, u.email, u.phone as user_phone
                  FROM " . $this->table . " d
                  JOIN orders o ON d.order_id = o.id
                  JOIN users u ON o.user_id = u.id
                  WHERE d.id = :id";

        
        $stmt = $this->conn->prepare($query);

        
        $stmt->bindParam(':id', $id);

        
        $stmt->execute();

        return $stmt->fetch();
    }

    
    public function getDeliveryByOrderId($orderId) {
        
        $query = "SELECT * FROM " . $this->table . " WHERE order_id = :order_id";

       
        $stmt = $this->conn->prepare($query);

        
        $stmt->bindParam(':order_id', $orderId);

        
        $stmt->execute();

        return $stmt->fetch();
    }

    
    public function updateDeliveryStatus($id, $status) {
        // Get current delivery status
        $query = "SELECT d.*, o.status as order_status
                  FROM " . $this->table . " d
                  JOIN orders o ON d.order_id = o.id
                  WHERE d.id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
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
            
            if ($status === 'delivered' && $currentStatus !== 'delivered') {
                
                $this->updateProductQuantities($orderId, -1); 
            } else if ($status === 'returned' && $currentStatus === 'delivered') {
                
                $this->updateProductQuantities($orderId, 1); 
            }

            return true;
        }

        return false;
    }

    
    public function updateOrderStatus($orderId, $status) {
        
        $query = "UPDATE orders
                  SET status = :status
                  WHERE id = :id";

        
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $orderId);

        
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    
    public function getDeliveryStatusCounts() {
        // Query
        $query = "SELECT delivery_status, COUNT(*) as count
                  FROM " . $this->table . "
                  GROUP BY delivery_status";

        
        $stmt = $this->conn->prepare($query);

       
        $stmt->execute();

        $counts = [];
        while ($row = $stmt->fetch()) {
            $counts[$row['delivery_status']] = $row['count'];
        }

        return $counts;
    }

   
    public function getDeliveryPersonnel() {
        
        $query = "SELECT id, first_name, last_name, email, phone, created_at
                  FROM users
                  WHERE is_delivery = 1
                  ORDER BY first_name, last_name";

       
        $stmt = $this->conn->prepare($query);

        
        $stmt->execute();

        return $stmt->fetchAll();
    }

    
    public function assignDelivery($deliveryId, $personnelId) {
        
        $query = "UPDATE " . $this->table . "
                  SET personnel_id = :personnel_id,
                      delivery_status = 'assigned'
                  WHERE id = :id";

        
        $stmt = $this->conn->prepare($query);

       
        $stmt->bindParam(':personnel_id', $personnelId);
        $stmt->bindParam(':id', $deliveryId);

       
        if ($stmt->execute()) {
            
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

    
    public function getDeliveryPersonnelById($id) {
       
        $query = "SELECT * FROM " . $this->usersTable . "
                  WHERE id = :id AND is_delivery = 1";

        
        $stmt = $this->conn->prepare($query);

       
        $stmt->bindParam(':id', $id);

        
        $stmt->execute();

        return $stmt->fetch();
    }

    
    public function createDeliveryPersonnel($data) {
        // Hash password
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        
        $query = "INSERT INTO " . $this->usersTable . "
                  SET first_name = :first_name,
                      last_name = :last_name,
                      email = :email,
                      password = :password,
                      phone = :phone,
                      address = :address,
                      city = :city,
                      postal_code = :postal_code,
                      is_delivery = :is_delivery";

        
        $stmt = $this->conn->prepare($query);

       
        $stmt->bindParam(':first_name', $data['first_name']);
        $stmt->bindParam(':last_name', $data['last_name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':address', $data['address']);
        $stmt->bindParam(':city', $data['city']);
        $stmt->bindParam(':postal_code', $data['postal_code']);
        $stmt->bindParam(':is_delivery', $data['is_delivery']);

       
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }

        return false;
    }

   
    public function updateDeliveryPersonnel($id, $data) {
       
        $query = "UPDATE " . $this->usersTable . "
                  SET first_name = :first_name,
                      last_name = :last_name,
                      email = :email,
                      phone = :phone,
                      address = :address,
                      city = :city,
                      postal_code = :postal_code
                  WHERE id = :id AND is_delivery = 1";

       
        $stmt = $this->conn->prepare($query);

        
        $stmt->bindParam(':first_name', $data['first_name']);
        $stmt->bindParam(':last_name', $data['last_name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':address', $data['address']);
        $stmt->bindParam(':city', $data['city']);
        $stmt->bindParam(':postal_code', $data['postal_code']);
        $stmt->bindParam(':id', $id);

       
        $result = $stmt->execute();

        
        if (!empty($data['password'])) {
            
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

           
            $passwordQuery = "UPDATE " . $this->usersTable . "
                              SET password = :password
                              WHERE id = :id AND is_delivery = 1";

           
            $passwordStmt = $this->conn->prepare($passwordQuery);

            
            $passwordStmt->bindParam(':password', $hashedPassword);
            $passwordStmt->bindParam(':id', $id);

           
            $passwordResult = $passwordStmt->execute();

            
            return $result && $passwordResult;
        }

        return $result;
    }

   
    public function deleteDeliveryPersonnel($id) {
        
        $unassignQuery = "UPDATE " . $this->table . "
                          SET personnel_id = NULL,
                              delivery_status = 'pending'
                          WHERE personnel_id = :personnel_id";

      
        $unassignStmt = $this->conn->prepare($unassignQuery);

        
        $unassignStmt->bindParam(':personnel_id', $id);

        
        $unassignStmt->execute();

       
        $deleteQuery = "DELETE FROM " . $this->usersTable . "
                        WHERE id = :id AND is_delivery = 1";

       
        $deleteStmt = $this->conn->prepare($deleteQuery);

       
        $deleteStmt->bindParam(':id', $id);

        
        return $deleteStmt->execute();
    }

    
    public function emailExists($email, $excludeId = null) {
       
        $query = "SELECT id FROM " . $this->usersTable . "
                  WHERE email = :email";

       
        if ($excludeId !== null) {
            $query .= " AND id != :exclude_id";
        }

        
        $stmt = $this->conn->prepare($query);

        
        $stmt->bindParam(':email', $email);

        if ($excludeId !== null) {
            $stmt->bindParam(':exclude_id', $excludeId);
        }

        
        $stmt->execute();

        
        return $stmt->rowCount() > 0;
    }

    
    public function getActiveOrdersCount($personnelId) {
       
        $query = "SELECT COUNT(*) as count FROM " . $this->table . "
                  WHERE personnel_id = :personnel_id
                  AND delivery_status IN ('assigned', 'in_transit')";

       
        $stmt = $this->conn->prepare($query);

       
        $stmt->bindParam(':personnel_id', $personnelId);

        
        $stmt->execute();

       
        $result = $stmt->fetch();

        return $result ? (int)$result['count'] : 0;
    }

    
    public function getDeliveriesByPersonnelId($personnelId) {
        
        $query = "SELECT d.*, o.id as order_id, o.total_amount, o.created_at as order_date,
                         u.first_name, u.last_name, u.email
                  FROM " . $this->table . " d
                  JOIN orders o ON d.order_id = o.id
                  JOIN users u ON o.user_id = u.id
                  WHERE d.personnel_id = :personnel_id
                  ORDER BY o.created_at DESC";

      
        $stmt = $this->conn->prepare($query);

       
        $stmt->bindParam(':personnel_id', $personnelId);

       
        $stmt->execute();

        return $stmt->fetchAll();
    }

    
    public function getDeliveryStatsByPersonnelId($personnelId) {
       
        $query = "SELECT delivery_status, COUNT(*) as count
                  FROM " . $this->table . "
                  WHERE personnel_id = :personnel_id
                  GROUP BY delivery_status";

        
        $stmt = $this->conn->prepare($query);

       
        $stmt->bindParam(':personnel_id', $personnelId);

        
        $stmt->execute();

        
        $stats = [
            'total' => 0,
            'assigned' => 0,
            'in_transit' => 0,
            'delivered' => 0,
            'returned' => 0
        ];

       
        while ($row = $stmt->fetch()) {
            $stats[$row['delivery_status']] = (int)$row['count'];
            $stats['total'] += (int)$row['count'];
        }

        
        if ($stats['total'] > 0) {
            $completed = $stats['delivered'] + $stats['returned'];
            $stats['completion_rate'] = round(($completed / $stats['total']) * 100);
        }

        return $stats;
    }

    
    public function getOrderItems($orderId) {
       
        $query = "SELECT oi.*, p.name, p.image_path
                  FROM order_items oi
                  JOIN products p ON oi.product_id = p.id
                  WHERE oi.order_id = :order_id";

        
        $stmt = $this->conn->prepare($query);

        
        $stmt->bindParam(':order_id', $orderId);

       
        $stmt->execute();

        return $stmt->fetchAll();
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
