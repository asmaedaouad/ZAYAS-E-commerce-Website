<?php
require_once __DIR__ . '/../models/AdminOrderModel.php';

class AdminOrderController {
    private $orderModel;

    public function __construct($db) {
        $this->orderModel = new AdminOrderModel($db);
    }

    // Get all orders
    public function getOrders($filters = []) {
        return $this->orderModel->getFilteredOrders($filters);
    }

    // Get order by ID
    public function getOrderById($id) {
        return $this->orderModel->getOrderById($id);
    }

    // Get order items
    public function getOrderItems($orderId) {
        return $this->orderModel->getOrderItems($orderId);
    }

    // Update order status
    public function updateOrderStatus($id, $status) {
        return $this->orderModel->updateOrderStatus($id, $status);
    }

    // Get order status counts
    public function getOrderStatusCounts() {
        return $this->orderModel->getOrderStatusCounts();
    }

    // Get delivery information for an order
    public function getDeliveryInfo($orderId) {
        return $this->orderModel->getDeliveryInfo($orderId);
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
}
?>
