<?php
class AdminCustomerModel {
    private $conn;
    private $table = 'users';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get all regular customers (not admins, not delivery personnel)
    public function getCustomers() {
        // Base query
        $query = "SELECT * FROM " . $this->table . "
                  WHERE is_admin = 0 AND is_delivery = 0
                  ORDER BY created_at DESC";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

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
        $query = "SELECT w.*, p.name, p.price, p.image_path
                  FROM wishlist w
                  JOIN products p ON w.product_id = p.id
                  WHERE w.user_id = :user_id";

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
        $query = "SELECT c.*, p.name, p.price, p.image_path
                  FROM cart c
                  JOIN products p ON c.product_id = p.id
                  WHERE c.user_id = :user_id";

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
        // Build update query based on provided data
        $updateFields = [];
        $params = [];

        // Only update fields that are provided
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

        // If no fields to update, return false
        if (empty($updateFields)) {
            return false;
        }

        // Build the query
        $query = "UPDATE " . $this->table . " SET " . implode(", ", $updateFields) . " WHERE id = :id";
        $params[':id'] = $id;

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        foreach ($params as $param => $value) {
            $stmt->bindValue($param, $value);
        }

        // Execute query
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Delete customer
    public function deleteCustomer($id) {
        // First check if customer exists and is a regular customer (not admin, not delivery)
        $customer = $this->getCustomerById($id);
        if (!$customer || $customer['is_admin'] == 1 || $customer['is_delivery'] == 1) {
            return false;
        }

        // Delete query
        $query = "DELETE FROM " . $this->table . " WHERE id = :id AND is_admin = 0 AND is_delivery = 0";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind parameter
        $stmt->bindParam(':id', $id);

        // Execute query
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}
?>
