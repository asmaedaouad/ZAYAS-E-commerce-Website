<?php
class UserModel {
    private $conn;
    private $table = 'users';

    public function __construct($db) {
        $this->conn = $db;
    }

    
    public function login($email, $password) {
        
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE email = :email AND is_delivery = 1";

        
        $stmt = $this->conn->prepare($query);

        
        $stmt->bindParam(':email', $email);

        
        $stmt->execute();

        $user = $stmt->fetch();

        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }

        return false;
    }

    
    public function getUserById($id) {
        
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE id = :id AND is_delivery = 1";

        
        $stmt = $this->conn->prepare($query);

        
        $stmt->bindParam(':id', $id);

        
        $stmt->execute();

        return $stmt->fetch();
    }

    
    public function updateProfile($id, $firstName, $lastName, $phone) {
       
        $query = "UPDATE " . $this->table . " 
                  SET first_name = :first_name, 
                      last_name = :last_name, 
                      phone = :phone 
                  WHERE id = :id AND is_delivery = 1";

        
        $stmt = $this->conn->prepare($query);

        
        $stmt->bindParam(':first_name', $firstName);
        $stmt->bindParam(':last_name', $lastName);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':id', $id);

        
        return $stmt->execute();
    }

    
    public function updatePassword($id, $password) {
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        
        $query = "UPDATE " . $this->table . " 
                  SET password = :password 
                  WHERE id = :id AND is_delivery = 1";

        
        $stmt = $this->conn->prepare($query);

        
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':id', $id);

        
        return $stmt->execute();
    }
}
?>
