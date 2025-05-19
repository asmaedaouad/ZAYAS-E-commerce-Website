<?php
class DeliveryModel {
    private $conn;
    private $table = 'delivery';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get deliveries assigned to a specific delivery person
    public function getAssignedDeliveries($personnelId) {
        // Query
        $query = "SELECT d.*, o.id as order_id, o.total_amount, o.created_at as order_date,
                         u.first_name, u.last_name, u.email, u.phone as user_phone,
                         u.address as user_address, u.city as user_city, u.postal_code as user_postal_code
                  FROM " . $this->table . " d
                  JOIN orders o ON d.order_id = o.id
                  JOIN users u ON o.user_id = u.id
                  WHERE d.personnel_id = :personnel_id
                  ORDER BY d.delivery_status ASC, o.created_at ASC";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind parameter
        $stmt->bindParam(':personnel_id', $personnelId);

        // Execute query
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Get delivery by ID
    public function getDeliveryById($id, $personnelId) {
        // Query
        $query = "SELECT d.*, o.id as order_id, o.total_amount, o.status as order_status, o.created_at as order_date,
                         u.first_name, u.last_name, u.email, u.phone as user_phone,
                         u.address as user_address, u.city as user_city, u.postal_code as user_postal_code
                  FROM " . $this->table . " d
                  JOIN orders o ON d.order_id = o.id
                  JOIN users u ON o.user_id = u.id
                  WHERE d.id = :id AND d.personnel_id = :personnel_id";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':personnel_id', $personnelId);

        // Execute query
        $stmt->execute();

        return $stmt->fetch();
    }

    // Get order items for a delivery
    public function getOrderItems($orderId) {
        // Query
        $query = "SELECT oi.*, p.name as product_name, p.image_path
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

    // Update delivery status
    public function updateDeliveryStatus($id, $personnelId, $status) {
        // First check if this delivery belongs to this personnel
        $query = "SELECT d.*, o.status as order_status
                  FROM " . $this->table . " d
                  JOIN orders o ON d.order_id = o.id
                  WHERE d.id = :id AND d.personnel_id = :personnel_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':personnel_id', $personnelId);
        $stmt->execute();

        $delivery = $stmt->fetch();
        if (!$delivery) {
            return false; // Not authorized
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
            // Also update the order status
            $this->updateOrderStatus($id, $status);

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
    private function updateOrderStatus($deliveryId, $deliveryStatus) {
        // Get order ID
        $query = "SELECT order_id FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $deliveryId);
        $stmt->execute();
        $result = $stmt->fetch();

        if (!$result) {
            return false;
        }

        $orderId = $result['order_id'];
        $orderStatus = $deliveryStatus; // In most cases, they match

        // Update order status
        $query = "UPDATE orders SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $orderStatus);
        $stmt->bindParam(':id', $orderId);

        return $stmt->execute();
    }

    // Get delivery status counts for a delivery person
    public function getDeliveryStatusCounts($personnelId) {
        // Query
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

        $counts = [];
        while ($row = $stmt->fetch()) {
            $counts[$row['delivery_status']] = $row['count'];
        }

        return $counts;
    }

    // Get total count of pending orders in the database
    public function getTotalPendingOrdersCount() {
        // Query to get all pending orders
        $query = "SELECT COUNT(*) as count
                  FROM " . $this->table . "
                  WHERE delivery_status = 'pending'";

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Execute query
        $stmt->execute();

        $result = $stmt->fetch();
        return $result ? (int)$result['count'] : 0;
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
