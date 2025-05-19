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
        $query = "SELECT id, first_name, last_name, email, address, city, postal_code, phone, is_admin, is_delivery, password
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

    // Get user by email
    public function getUserByEmail($email) {
        // Query to get user
        $query = "SELECT id, first_name, last_name, email, is_admin, is_delivery
                  FROM " . $this->table . "
                  WHERE email = :email";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind parameter
        $stmt->bindParam(':email', $email);

        // Execute query
        $stmt->execute();

        // Return user data
        return $stmt->fetch();
    }

    // Create password reset token
    public function createPasswordResetToken($userId, $token) {
        // Delete any existing tokens for this user
        $this->deleteExistingTokens($userId);

        // Set expiration time (15 minutes from now)
        $expiresAt = date('Y-m-d H:i:s', strtotime('+15 minutes'));

        // Insert query
        $query = "INSERT INTO password_reset_tokens
                  SET user_id = :user_id,
                      token = :token,
                      expires_at = :expires_at";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':expires_at', $expiresAt);

        // Execute query
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Delete existing tokens for a user
    private function deleteExistingTokens($userId) {
        // Delete query
        $query = "DELETE FROM password_reset_tokens
                  WHERE user_id = :user_id";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind parameter
        $stmt->bindParam(':user_id', $userId);

        // Execute query
        $stmt->execute();
    }

    // Verify password reset token
    public function verifyPasswordResetToken($token) {
        // Query to check if token exists and is valid
        $query = "SELECT user_id FROM password_reset_tokens
                  WHERE token = :token
                  AND expires_at > NOW()
                  AND used = 0";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind parameter
        $stmt->bindParam(':token', $token);

        // Execute query
        $stmt->execute();

        // Get result
        $result = $stmt->fetch();

        // Return user ID if token is valid
        return $result ? $result['user_id'] : false;
    }

    // Mark token as used
    public function markTokenAsUsed($token) {
        // Update query
        $query = "UPDATE password_reset_tokens
                  SET used = 1
                  WHERE token = :token";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind parameter
        $stmt->bindParam(':token', $token);

        // Execute query
        return $stmt->execute();
    }

    // Reset password with token
    public function resetPasswordWithToken($token, $password) {
        // Verify token and get user ID
        $userId = $this->verifyPasswordResetToken($token);

        if (!$userId) {
            return false;
        }

        // Update password
        if ($this->updatePassword($userId, $password)) {
            // Mark token as used
            $this->markTokenAsUsed($token);
            return true;
        }

        return false;
    }

    // Update delivery personnel profile
    public function updateDeliveryProfile($id, $firstName, $lastName, $phone) {
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
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}
?>
