<?php
class AdminCustomerModel {
    private $conn;
    private $table = 'users';

    public function __construct($db) {
        $this->conn = $db;
    }

    
    public function getCustomers() {
        // Base query
        $query = "SELECT * FROM " . $this->table . "
                  WHERE is_admin = 0 AND is_delivery = 0
                  ORDER BY created_at DESC";

        
        $stmt = $this->conn->prepare($query);

        
        $stmt->execute();

        return $stmt->fetchAll();
    }

    
    public function getCustomerById($id) {
        // Query
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";

        
        $stmt = $this->conn->prepare($query);

        
        $stmt->bindParam(':id', $id);

        
        $stmt->execute();

        return $stmt->fetch();
    }

    
    public function getCustomerOrders($userId) {
        
        $query = "SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC";

        
        $stmt = $this->conn->prepare($query);

        
        $stmt->bindParam(':user_id', $userId);

        
        $stmt->execute();

        return $stmt->fetchAll();
    }

    
    public function getCustomerWishlist($userId) {
       
        $query = "SELECT w.*, p.name, p.price, p.image_path
                  FROM wishlist w
                  JOIN products p ON w.product_id = p.id
                  WHERE w.user_id = :user_id";

        
        $stmt = $this->conn->prepare($query);

        
        $stmt->bindParam(':user_id', $userId);

        
        $stmt->execute();

        return $stmt->fetchAll();
    }

   
    public function getCustomerCart($userId) {
        
        $query = "SELECT c.*, p.name, p.price, p.image_path
                  FROM cart c
                  JOIN products p ON c.product_id = p.id
                  WHERE c.user_id = :user_id";

        
        $stmt = $this->conn->prepare($query);

        
        $stmt->bindParam(':user_id', $userId);

        
        $stmt->execute();

        return $stmt->fetchAll();
    }

    
    public function updateCustomer($id, $data) {
        
        $updateFields = [];
        $params = [];

        
        if (isset($data['first_name'])) {
            $updateFields[] = "first_name = :first_name";
            $params[':first_name'] = $data['first_name'];
        }

        if (isset($data['last_name'])) {
            $updateFields[] = "last_name = :last_name";
            $params[':last_name'] = $data['last_name'];
        }

        if (isset($data['email'])) {
            $updateFields[] = "email = :email";
            $params[':email'] = $data['email'];
        }

        if (isset($data['phone'])) {
            $updateFields[] = "phone = :phone";
            $params[':phone'] = $data['phone'];
        }

        if (isset($data['address'])) {
            $updateFields[] = "address = :address";
            $params[':address'] = $data['address'];
        }

        if (isset($data['city'])) {
            $updateFields[] = "city = :city";
            $params[':city'] = $data['city'];
        }

        if (isset($data['password']) && !empty($data['password'])) {
            $updateFields[] = "password = :password";
            $params[':password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

       
        if (empty($updateFields)) {
            return false;
        }

        
        $query = "UPDATE " . $this->table . " SET " . implode(", ", $updateFields) . " WHERE id = :id";
        $params[':id'] = $id;

        
        $stmt = $this->conn->prepare($query);

        
        foreach ($params as $param => $value) {
            $stmt->bindValue($param, $value);
        }

        
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    
    public function deleteCustomer($id) {
       
        $customer = $this->getCustomerById($id);
        if (!$customer || $customer['is_admin'] == 1 || $customer['is_delivery'] == 1) {
            return false;
        }

        
        $query = "DELETE FROM " . $this->table . " WHERE id = :id AND is_admin = 0 AND is_delivery = 0";

        
        $stmt = $this->conn->prepare($query);

        
        $stmt->bindParam(':id', $id);

        
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}
?>
