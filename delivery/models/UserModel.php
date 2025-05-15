<?php
class UserModel {
    private $conn;
    private $table = 'users';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Login delivery personnel
    public function login($email, $password) {
        // Query
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE email = :email AND is_delivery = 1";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind parameter
        $stmt->bindParam(':email', $email);

        // Execute query
        $stmt->execute();

        $user = $stmt->fetch();

        // Check if user exists and verify password
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }

        return false;
    }

    // Get delivery personnel by ID
    public function getUserById($id) {
        // Query
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE id = :id AND is_delivery = 1";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind parameter
        $stmt->bindParam(':id', $id);

        // Execute query
        $stmt->execute();

        return $stmt->fetch();
    }

    // Update delivery personnel profile
    public function updateProfile($id, $firstName, $lastName, $phone) {
        // Update query
        $query = "UPDATE " . $this->table . " 
                  SET first_name = :first_name, 
                      last_name = :last_name, 
                      phone = :phone 
                  WHERE id = :id AND is_delivery = 1";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':first_name', $firstName);
        $stmt->bindParam(':last_name', $lastName);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':id', $id);

        // Execute query
        return $stmt->execute();
    }

    // Update password
    public function updatePassword($id, $password) {
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Update query
        $query = "UPDATE " . $this->table . " 
                  SET password = :password 
                  WHERE id = :id AND is_delivery = 1";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':id', $id);

        // Execute query
        return $stmt->execute();
    }
}
?>
