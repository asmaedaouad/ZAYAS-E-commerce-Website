<?php
require_once __DIR__ . '/../models/DeliveryModel.php';

class DeliveryController {
    private $deliveryModel;

    public function __construct($db) {
        $this->deliveryModel = new DeliveryModel($db);
    }

    // Get assigned deliveries
    public function getAssignedDeliveries() {
        if (!isLoggedIn() || !isDelivery()) {
            redirect('/views/auth/login.php');
        }

        return $this->deliveryModel->getAssignedDeliveries($_SESSION['user_id']);
    }

    // Get delivery by ID
    public function getDeliveryById($id) {
        if (!isLoggedIn() || !isDelivery()) {
            redirect('/views/auth/login.php');
        }

        $delivery = $this->deliveryModel->getDeliveryById($id, $_SESSION['user_id']);

        if (!$delivery) {
            return [
                'error' => 'Delivery not found or not assigned to you'
            ];
        }

        
        $orderItems = $this->deliveryModel->getOrderItems($delivery['order_id']);

        return [
            'delivery' => $delivery,
            'order_items' => $orderItems
        ];
    }

    
    public function updateDeliveryStatus() {
        if (!isLoggedIn() || !isDelivery()) {
            redirect('/views/auth/login.php');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $deliveryId = isset($_POST['delivery_id']) ? (int)$_POST['delivery_id'] : 0;
            $status = isset($_POST['status']) ? sanitize($_POST['status']) : '';

            
            $errors = [];

            if ($deliveryId <= 0) {
                $errors[] = 'Invalid delivery ID';
            }

            if (empty($status)) {
                $errors[] = 'Status is required';
            } elseif (!in_array($status, ['assigned', 'in_transit', 'delivered', 'cancelled', 'returned'])) {
                $errors[] = 'Invalid status';
            }

            
            if (empty($errors)) {
                if ($this->deliveryModel->updateDeliveryStatus($deliveryId, $_SESSION['user_id'], $status)) {
                    return [
                        'success' => 'Delivery status updated successfully'
                    ];
                } else {
                    $errors[] = 'Failed to update delivery status';
                }
            }

            
            return [
                'errors' => $errors
            ];
        }

        
        return [
            'errors' => ['Invalid request']
        ];
    }

    
    public function getDeliveryStatusCounts() {
        if (!isLoggedIn() || !isDelivery()) {
            redirect('/views/auth/login.php');
        }

        return $this->deliveryModel->getDeliveryStatusCounts($_SESSION['user_id']);
    }

    
    public function getTotalPendingOrdersCount() {
        if (!isLoggedIn() || !isDelivery()) {
            redirect('/views/auth/login.php');
        }

        return $this->deliveryModel->getTotalPendingOrdersCount();
    }

    
    public function formatCurrency($amount) {
        return number_format($amount, 2) . 'DH';
    }

    
    public function formatDate($date) {
        return date('M d, Y', strtotime($date));
    }

    
    public function getStatusBadgeClass($status) {
        switch ($status) {
            case 'pending':
                return 'bg-secondary';
            case 'assigned':
                return 'bg-info';
            case 'in_transit':
                return 'bg-primary';
            case 'delivered':
                return 'bg-success';
            case 'cancelled':
                return 'bg-danger';
            case 'returned':
                return 'bg-warning';
            default:
                return 'bg-secondary';
        }
    }

    
    public function formatStatus($status) {
        return ucfirst(str_replace('_', ' ', $status));
    }

    
    public function getAvailableStatusOptions($currentStatus) {
        $allStatuses = [
            'assigned' => 'Assigned',
            'in_transit' => 'In Transit',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
            'returned' => 'Returned'
        ];

        
        $validTransitions = [
            'assigned' => ['assigned', 'in_transit'], // Delivery person can accept the order (in_transit)
            'in_transit' => ['in_transit', 'delivered'], // Delivery person can mark as delivered
            'delivered' => ['delivered', 'returned'], // Delivery person can mark as returned after delivery
            'cancelled' => ['cancelled'], // Final state
            'returned' => ['returned'] // Final state
        ];

        
        $validOptions = isset($validTransitions[$currentStatus]) ? $validTransitions[$currentStatus] : [];

        // Filter all statuses to only include valid options
        $options = [];
        foreach ($validOptions as $status) {
            $options[$status] = $allStatuses[$status];
        }

        return $options;
    }
}
?>

