<?php
require_once __DIR__ . '/../models/AdminOrderModel.php';

class AdminOrderController {
    private $orderModel;

    public function __construct($db) {
        $this->orderModel = new AdminOrderModel($db);
    }

    
    public function getOrders($filters = []) {
        return $this->orderModel->getFilteredOrders($filters);
    }

    
    public function getOrderById($id) {
        return $this->orderModel->getOrderById($id);
    }

    
    public function getOrderItems($orderId) {
        return $this->orderModel->getOrderItems($orderId);
    }

    
    public function updateOrderStatus($id, $status) {
        return $this->orderModel->updateOrderStatus($id, $status);
    }

    
    public function getOrderStatusCounts() {
        return $this->orderModel->getOrderStatusCounts();
    }

    
    public function getDeliveryInfo($orderId) {
        return $this->orderModel->getDeliveryInfo($orderId);
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

    
    public function formatStatus($status) {
        return ucfirst(str_replace('_', ' ', $status));
    }

    
    public function getAvailableStatusOptions($currentStatus) {
        $allStatuses = [
            'pending' => 'Pending',
            'assigned' => 'Assigned',
            'in_transit' => 'In Transit',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
            'returned' => 'Returned'
        ];

        
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
            // Fallback to all statuses if current status is unknown !!
            $availableOptions = $allStatuses;
        }

        return $availableOptions;
    }
}
?>
