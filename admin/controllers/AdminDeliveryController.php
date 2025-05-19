<?php
require_once __DIR__ . '/../models/AdminDeliveryModel.php';

class AdminDeliveryController {
    private $deliveryModel;

    public function __construct($db) {
        $this->deliveryModel = new AdminDeliveryModel($db);
    }

    // Get deliveries with optional filtering
    public function getDeliveries($filters = []) {
        return $this->deliveryModel->getDeliveries($filters);
    }

    // Get delivery by ID
    public function getDeliveryById($id) {
        return $this->deliveryModel->getDeliveryById($id);
    }

    // Get delivery by order ID
    public function getDeliveryByOrderId($orderId) {
        return $this->deliveryModel->getDeliveryByOrderId($orderId);
    }

    // Update delivery status
    public function updateDeliveryStatus($id, $status) {
        return $this->deliveryModel->updateDeliveryStatus($id, $status);
    }

    // Update order status based on delivery status
    public function updateOrderStatus($orderId, $status) {
        return $this->deliveryModel->updateOrderStatus($orderId, $status);
    }

    // Get delivery status counts
    public function getDeliveryStatusCounts() {
        return $this->deliveryModel->getDeliveryStatusCounts();
    }

    // Get delivery personnel
    public function getDeliveryPersonnel() {
        return $this->deliveryModel->getDeliveryPersonnel();
    }

    // Assign delivery to personnel
    public function assignDelivery($deliveryId, $personnelId) {
        return $this->deliveryModel->assignDelivery($deliveryId, $personnelId);
    }

    // Format currency
    public function formatCurrency($amount) {
        return number_format($amount, 2) . 'DH';
    }

    // Format date
    public function formatDate($date) {
        return date('M d, Y', strtotime($date));
    }

    // Get status badge class
    public function getStatusBadgeClass($status) {
        switch ($status) {
            case 'pending':
                return 'badge-pending';
            case 'assigned':
                return 'badge-assigned';
            case 'in_transit':
                return 'badge-in-transit';
            case 'delivered':
                return 'badge-delivered';
            case 'cancelled':
                return 'badge-cancelled';
            case 'returned':
                return 'badge-returned';
            default:
                return 'badge-secondary';
        }
    }

    // Format status for display
    public function formatStatus($status) {
        return ucfirst(str_replace('_', ' ', $status));
    }

    // Get available status options for current status
    public function getAvailableStatusOptions($currentStatus) {
        $allStatuses = [
            'pending' => 'Pending',
            'assigned' => 'Assigned',
            'in_transit' => 'In Transit',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
            'returned' => 'Returned'
        ];

        // Define valid status transitions
        $validTransitions = [
            'pending' => ['pending', 'assigned', 'cancelled'],
            'assigned' => ['assigned', 'in_transit', 'cancelled'],
            'in_transit' => ['in_transit', 'delivered', 'cancelled'],
            'delivered' => ['delivered', 'returned'],
            'cancelled' => ['cancelled'],
            'returned' => ['returned']
        ];

        $availableOptions = [];

        if (isset($validTransitions[$currentStatus])) {
            foreach ($validTransitions[$currentStatus] as $status) {
                $availableOptions[$status] = $allStatuses[$status];
            }
        } else {
            // Fallback to all statuses if current status is unknown
            $availableOptions = $allStatuses;
        }

        return $availableOptions;
    }

    // Get delivery personnel by ID
    public function getDeliveryPersonnelById($id) {
        return $this->deliveryModel->getDeliveryPersonnelById($id);
    }

    // Create new delivery personnel
    public function createDeliveryPersonnel($data) {
        return $this->deliveryModel->createDeliveryPersonnel($data);
    }

    // Update delivery personnel
    public function updateDeliveryPersonnel($id, $data) {
        return $this->deliveryModel->updateDeliveryPersonnel($id, $data);
    }

    // Delete delivery personnel
    public function deleteDeliveryPersonnel($id) {
        return $this->deliveryModel->deleteDeliveryPersonnel($id);
    }

    // Check if email exists (for validation)
    public function emailExists($email, $excludeId = null) {
        return $this->deliveryModel->emailExists($email, $excludeId);
    }

    // Get active orders count for a delivery personnel
    public function getActiveOrdersCount($personnelId) {
        return $this->deliveryModel->getActiveOrdersCount($personnelId);
    }

    // Get deliveries assigned to a specific personnel
    public function getDeliveriesByPersonnelId($personnelId) {
        return $this->deliveryModel->getDeliveriesByPersonnelId($personnelId);
    }

    // Get delivery statistics for a specific personnel
    public function getDeliveryStatsByPersonnelId($personnelId) {
        return $this->deliveryModel->getDeliveryStatsByPersonnelId($personnelId);
    }
}
?>
