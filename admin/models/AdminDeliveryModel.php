<?php
class AdminDeliveryModel {
    private $conn;
    private $table = 'delivery';
    private $usersTable = 'users';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get all deliveries with optional filtering
    public function getDeliveries($filters = []) {
        // Base query
        $query = "SELECT d.*, o.id as order_id, o.total_amount, o.created_at as order_date,
                         u.first_name, u.last_name, u.email
                  FROM " . $this->table . " d
                  JOIN orders o ON d.order_id = o.id
                  JOIN users u ON o.user_id = u.id";

        // Add filters
        $conditions = [];
        $params = [];

        // Filter by status
        if (isset($filters['status']) && !empty($filters['status'])) {
            $conditions[] = "d.delivery_status = :status";
            $params[':status'] = $filters['status'];
        }

        // Filter by date range
        if (isset($filters['date_from']) && !empty($filters['date_from'])) {
            $conditions[] = "DATE(o.created_at) >= :date_from";
            $params[':date_from'] = $filters['date_from'];
        }

        if (isset($filters['date_to']) && !empty($filters['date_to'])) {
            $conditions[] = "DATE(o.created_at) <= :date_to";
            $params[':date_to'] = $filters['date_to'];
        }

        // Search by customer name or email
        if (isset($filters['search']) && !empty($filters['search'])) {
            $conditions[] = "(u.first_name LIKE :search OR u.last_name LIKE :search OR u.email LIKE :search OR CONCAT(u.first_name, ' ', u.last_name) LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        // Add conditions to query
        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        // Add order by
        $query .= " ORDER BY o.created_at DESC";

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

    // Get delivery by ID
    public function getDeliveryById($id) {
        // Query
        $query = "SELECT d.*, o.id as order_id, o.total_amount, o.status as order_status, o.created_at as order_date,
                         u.first_name, u.last_name, u.email, u.phone as user_phone
                  FROM " . $this->table . " d
                  JOIN orders o ON d.order_id = o.id
                  JOIN users u ON o.user_id = u.id
                  WHERE d.id = :id";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind parameter
        $stmt->bindParam(':id', $id);

        // Execute query
        $stmt->execute();

        return $stmt->fetch();
    }

    // Get delivery by order ID
    public function getDeliveryByOrderId($orderId) {
        // Query
        $query = "SELECT * FROM " . $this->table . " WHERE order_id = :order_id";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind parameter
        $stmt->bindParam(':order_id', $orderId);

        // Execute query
        $stmt->execute();

        return $stmt->fetch();
    }

    // Update delivery status
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

        // Get the current status before updating
        $currentStatus = $delivery['delivery_status'];
        $orderId = $delivery['order_id'];

        // Update query
        $query = "UPDATE " . $this->table . "
                  SET delivery_status = :status
                  WHERE id = :id";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);

        // Execute query
        if ($stmt->execute()) {
            // Update product quantities based on status change
            if ($status === 'delivered' && $currentStatus !== 'delivered') {
                // When order is delivered, decrease product quantities
                $this->updateProductQuantities($orderId, -1); // Negative multiplier to decrease
            } else if ($status === 'returned' && $currentStatus === 'delivered') {
                // When order is returned after being delivered, increase product quantities
                $this->updateProductQuantities($orderId, 1); // Positive multiplier to increase
            }

            return true;
        }

        return false;
    }

    // Update order status based on delivery status
    public function updateOrderStatus($orderId, $status) {
        // Update query
        $query = "UPDATE orders
                  SET status = :status
                  WHERE id = :id";

        // Prepare statement
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

    // Get delivery status counts
    public function getDeliveryStatusCounts() {
        // Query
        $query = "SELECT delivery_status, COUNT(*) as count
                  FROM " . $this->table . "
                  GROUP BY delivery_status";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Execute query
        $stmt->execute();

        $counts = [];
        while ($row = $stmt->fetch()) {
            $counts[$row['delivery_status']] = $row['count'];
        }

        return $counts;
    }

    // Get delivery personnel (users with is_delivery = 1)
    public function getDeliveryPersonnel() {
        // Query
        $query = "SELECT id, first_name, last_name, email, phone, created_at
                  FROM users
                  WHERE is_delivery = 1
                  ORDER BY first_name, last_name";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Execute query
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Assign delivery to personnel
    public function assignDelivery($deliveryId, $personnelId) {
        // Update query
        $query = "UPDATE " . $this->table . "
                  SET personnel_id = :personnel_id,
                      delivery_status = 'assigned'
                  WHERE id = :id";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':personnel_id', $personnelId);
        $stmt->bindParam(':id', $deliveryId);

        // Execute query
        if ($stmt->execute()) {
            // Get order ID
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

    // Get delivery personnel by ID
    public function getDeliveryPersonnelById($id) {
        // Query
        $query = "SELECT * FROM " . $this->usersTable . "
                  WHERE id = :id AND is_delivery = 1";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind parameter
        $stmt->bindParam(':id', $id);

        // Execute query
        $stmt->execute();

        return $stmt->fetch();
    }

    // Create new delivery personnel
    public function createDeliveryPersonnel($data) {
        // Hash password
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        // Insert query
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

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':first_name', $data['first_name']);
        $stmt->bindParam(':last_name', $data['last_name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':address', $data['address']);
        $stmt->bindParam(':city', $data['city']);
        $stmt->bindParam(':postal_code', $data['postal_code']);
        $stmt->bindParam(':is_delivery', $data['is_delivery']);

        // Execute query
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }

        return false;
    }

    // Update delivery personnel
    public function updateDeliveryPersonnel($id, $data) {
        // Base update query
        $query = "UPDATE " . $this->usersTable . "
                  SET first_name = :first_name,
                      last_name = :last_name,
                      email = :email,
                      phone = :phone,
                      address = :address,
                      city = :city,
                      postal_code = :postal_code
                  WHERE id = :id AND is_delivery = 1";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':first_name', $data['first_name']);
        $stmt->bindParam(':last_name', $data['last_name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':address', $data['address']);
        $stmt->bindParam(':city', $data['city']);
        $stmt->bindParam(':postal_code', $data['postal_code']);
        $stmt->bindParam(':id', $id);

        // Execute query
        $result = $stmt->execute();

        // If password is provided, update it separately
        if (!empty($data['password'])) {
            // Hash password
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

            // Update password query
            $passwordQuery = "UPDATE " . $this->usersTable . "
                              SET password = :password
                              WHERE id = :id AND is_delivery = 1";

            // Prepare statement
            $passwordStmt = $this->conn->prepare($passwordQuery);

            // Bind parameters
            $passwordStmt->bindParam(':password', $hashedPassword);
            $passwordStmt->bindParam(':id', $id);

            // Execute query
            $passwordResult = $passwordStmt->execute();

            // Return true only if both updates succeed
            return $result && $passwordResult;
        }

        return $result;
    }

    // Delete delivery personnel
    public function deleteDeliveryPersonnel($id) {
        // First, unassign all deliveries assigned to this personnel
        $unassignQuery = "UPDATE " . $this->table . "
                          SET personnel_id = NULL,
                              delivery_status = 'pending'
                          WHERE personnel_id = :personnel_id";

        // Prepare statement
        $unassignStmt = $this->conn->prepare($unassignQuery);

        // Bind parameter
        $unassignStmt->bindParam(':personnel_id', $id);

        // Execute unassign query
        $unassignStmt->execute();

        // Now delete the personnel
        $deleteQuery = "DELETE FROM " . $this->usersTable . "
                        WHERE id = :id AND is_delivery = 1";

        // Prepare statement
        $deleteStmt = $this->conn->prepare($deleteQuery);

        // Bind parameter
        $deleteStmt->bindParam(':id', $id);

        // Execute delete query
        return $deleteStmt->execute();
    }

    // Check if email exists (for validation)
    public function emailExists($email, $excludeId = null) {
        // Query
        $query = "SELECT id FROM " . $this->usersTable . "
                  WHERE email = :email";

        // Add exclusion for updates
        if ($excludeId !== null) {
            $query .= " AND id != :exclude_id";
        }

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':email', $email);

        if ($excludeId !== null) {
            $stmt->bindParam(':exclude_id', $excludeId);
        }

        // Execute query
        $stmt->execute();

        // Return true if email exists
        return $stmt->rowCount() > 0;
    }

    // Get active orders count for a delivery personnel
    public function getActiveOrdersCount($personnelId) {
        // Query
        $query = "SELECT COUNT(*) as count FROM " . $this->table . "
                  WHERE personnel_id = :personnel_id
                  AND delivery_status IN ('assigned', 'in_transit')";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind parameter
        $stmt->bindParam(':personnel_id', $personnelId);

        // Execute query
        $stmt->execute();

        // Get result
        $result = $stmt->fetch();

        return $result ? (int)$result['count'] : 0;
    }

    // Get deliveries assigned to a specific personnel
    public function getDeliveriesByPersonnelId($personnelId) {
        // Query
        $query = "SELECT d.*, o.id as order_id, o.total_amount, o.created_at as order_date,
                         u.first_name, u.last_name, u.email
                  FROM " . $this->table . " d
                  JOIN orders o ON d.order_id = o.id
                  JOIN users u ON o.user_id = u.id
                  WHERE d.personnel_id = :personnel_id
                  ORDER BY o.created_at DESC";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind parameter
        $stmt->bindParam(':personnel_id', $personnelId);

        // Execute query
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Get delivery statistics for a specific personnel
    public function getDeliveryStatsByPersonnelId($personnelId) {
        // Query for status counts
        $query = "SELECT delivery_status, COUNT(*) as count
                  FROM " . $this->table . "
                  WHERE personnel_id = :personnel_id
                  GROUP BY delivery_status";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind parameter
        $stmt->bindParam(':personnel_id', $personnelId);

        // Execute query
        $stmt->execute();

        // Initialize stats array
        $stats = [
            'total' => 0,
            'assigned' => 0,
            'in_transit' => 0,
            'delivered' => 0,
            'returned' => 0
        ];

        // Process results
        while ($row = $stmt->fetch()) {
            $stats[$row['delivery_status']] = (int)$row['count'];
            $stats['total'] += (int)$row['count'];
        }

        // Calculate completion rate if there are any deliveries
        if ($stats['total'] > 0) {
            $completed = $stats['delivered'] + $stats['returned'];
            $stats['completion_rate'] = round(($completed / $stats['total']) * 100);
        }

        return $stats;
    }

    // Get order items
    public function getOrderItems($orderId) {
        // Query
        $query = "SELECT oi.*, p.name, p.image_path
                  FROM order_items oi
                  JOIN products p ON oi.product_id = p.id
                  WHERE oi.order_id = :order_id";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind parameter
        $stmt->bindParam(':order_id', $orderId);

        // Execute query
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Update product quantities for an order
    private function updateProductQuantities($orderId, $multiplier) {
        // Include ProductModel
        require_once __DIR__ . '/../../models/ProductModel.php';

        // Create ProductModel instance
        $productModel = new ProductModel($this->conn);

        // Get order items
        $orderItems = $this->getOrderItems($orderId);

        // Update quantity for each product
        foreach ($orderItems as $item) {
            $productId = $item['product_id'];
            $quantity = $item['quantity'];

            // Calculate quantity change (positive for increase, negative for decrease)
            $quantityChange = $quantity * $multiplier;

            // Update product quantity
            $productModel->updateProductQuantity($productId, $quantityChange);
        }

        return true;
    }
}
?>
