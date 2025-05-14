<?php
class AdminCustomerModel {
    private $conn;
    private $table = 'users';
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Get all customers (non-admin users)
    public function getCustomers($filters = []) {
        // Base query
        $query = "SELECT * FROM " . $this->table . " WHERE is_admin = 0";
        
        // Add filters
        $conditions = [];
        $params = [];
        
        // Filter by delivery personnel
        if (isset($filters['is_delivery']) && $filters['is_delivery'] !== '') {
            $conditions[] = "is_delivery = :is_delivery";
            $params[':is_delivery'] = $filters['is_delivery'];
        } else {
            // By default, exclude delivery personnel
            $conditions[] = "is_delivery = 0";
        }
        
        // Search by name or email
        if (isset($filters['search']) && !empty($filters['search'])) {
            $conditions[] = "(first_name LIKE :search OR last_name LIKE :search OR email LIKE :search OR CONCAT(first_name, ' ', last_name) LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        
        // Add conditions to query
        if (!empty($conditions)) {
            $query .= " AND " . implode(" AND ", $conditions);
        }
        
        // Add order by
        $query .= " ORDER BY created_at DESC";
        
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
    
    // Get customer by ID
    public function getCustomerById($id) {
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
    
    // Get customer orders
    public function getCustomerOrders($userId) {
        // Query
        $query = "SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        // Bind parameter
        $stmt->bindParam(':user_id', $userId);
        
        // Execute query
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    // Get customer wishlist
    public function getCustomerWishlist($userId) {
        // Query
        $query = "SELECT w.*, p.name, p.price, p.image_path, p.type 
                  FROM wishlist w
                  JOIN products p ON w.product_id = p.id
                  WHERE w.user_id = :user_id
                  ORDER BY w.created_at DESC";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        // Bind parameter
        $stmt->bindParam(':user_id', $userId);
        
        // Execute query
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    // Get customer cart
    public function getCustomerCart($userId) {
        // Query
        $query = "SELECT c.*, p.name, p.price, p.image_path, p.type 
                  FROM cart c
                  JOIN products p ON c.product_id = p.id
                  WHERE c.user_id = :user_id
                  ORDER BY c.updated_at DESC";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        // Bind parameter
        $stmt->bindParam(':user_id', $userId);
        
        // Execute query
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    // Update customer
    public function updateCustomer($id, $data) {
        // Update query
        $query = "UPDATE " . $this->table . " 
                  SET first_name = :first_name, 
                      last_name = :last_name, 
                      email = :email, 
                      address = :address, 
                      city = :city, 
                      postal_code = :postal_code, 
                      phone = :phone, 
                      is_delivery = :is_delivery 
                  WHERE id = :id";
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        // Sanitize and bind parameters
        $firstName = htmlspecialchars(strip_tags($data['first_name']));
        $lastName = htmlspecialchars(strip_tags($data['last_name']));
        $email = htmlspecialchars(strip_tags($data['email']));
        $address = htmlspecialchars(strip_tags($data['address'] ?? ''));
        $city = htmlspecialchars(strip_tags($data['city'] ?? ''));
        $postalCode = htmlspecialchars(strip_tags($data['postal_code'] ?? ''));
        $phone = htmlspecialchars(strip_tags($data['phone'] ?? ''));
        $isDelivery = isset($data['is_delivery']) ? (int)$data['is_delivery'] : 0;
        
        $stmt->bindParam(':first_name', $firstName);
        $stmt->bindParam(':last_name', $lastName);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':city', $city);
        $stmt->bindParam(':postal_code', $postalCode);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':is_delivery', $isDelivery);
        $stmt->bindParam(':id', $id);
        
        // Execute query
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
}
?>
