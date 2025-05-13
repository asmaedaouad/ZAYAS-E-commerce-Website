<?php
class UserModel {
    private $conn;
    private $table = 'users';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Register new user
    public function register($firstName, $lastName, $email, $password) {
        // Check if email already exists
        if ($this->emailExists($email)) {
            return false;
        }

        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert query
        $query = "INSERT INTO " . $this->table . " 
                  SET first_name = :first_name, 
                      last_name = :last_name, 
                      email = :email, 
                      password = :password";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':first_name', $firstName);
        $stmt->bindParam(':last_name', $lastName);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);

        // Execute query
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Login user
    public function login($email, $password) {
        // Query to check if email exists
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind parameter
        $stmt->bindParam(':email', $email);

        // Execute query
        $stmt->execute();

        // Get row count
        $num = $stmt->rowCount();

        // If email exists
        if ($num > 0) {
            // Get user data
            $row = $stmt->fetch();

            // Verify password
            if (password_verify($password, $row['password'])) {
                return $row;
            }
        }

        return false;
    }

    // Check if email exists
    public function emailExists($email) {
        // Query to check if email exists
        $query = "SELECT id FROM " . $this->table . " WHERE email = :email";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind parameter
        $stmt->bindParam(':email', $email);

        // Execute query
        $stmt->execute();

        // Get row count
        $num = $stmt->rowCount();

        // If email exists
        if ($num > 0) {
            return true;
        }

        return false;
    }

    // Get user by ID
    public function getUserById($id) {
        // Query to get user
        $query = "SELECT id, first_name, last_name, email, address, city, postal_code, phone, is_admin, is_delivery 
                  FROM " . $this->table . " 
                  WHERE id = :id";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind parameter
        $stmt->bindParam(':id', $id);

        // Execute query
        $stmt->execute();

        // Return user data
        return $stmt->fetch();
    }

    // Update user profile
    public function updateProfile($id, $firstName, $lastName, $address, $city, $postalCode, $phone) {
        // Update query
        $query = "UPDATE " . $this->table . " 
                  SET first_name = :first_name, 
                      last_name = :last_name, 
                      address = :address, 
                      city = :city, 
                      postal_code = :postal_code, 
                      phone = :phone 
                  WHERE id = :id";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':first_name', $firstName);
        $stmt->bindParam(':last_name', $lastName);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':city', $city);
        $stmt->bindParam(':postal_code', $postalCode);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':id', $id);

        // Execute query
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Update password
    public function updatePassword($id, $password) {
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Update query
        $query = "UPDATE " . $this->table . " 
                  SET password = :password 
                  WHERE id = :id";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':id', $id);

        // Execute query
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}
?>
