<?php
require_once __DIR__ . '/../models/AdminDeliveryModel.php';

class AdminDeliveryController {
    private $deliveryModel;

    public function __construct($db) {
        $this->deliveryModel = new AdminDeliveryModel($db);
    }

    
    public function getDeliveries($filters = []) {
        return $this->deliveryModel->getDeliveries($filters);
    }

    
    public function getDeliveryById($id) {
        return $this->deliveryModel->getDeliveryById($id);
    }

    
    public function getDeliveryByOrderId($orderId) {
        return $this->deliveryModel->getDeliveryByOrderId($orderId);
    }

    
    public function updateDeliveryStatus($id, $status) {
        return $this->deliveryModel->updateDeliveryStatus($id, $status);
    }

    
    public function updateOrderStatus($orderId, $status) {
        return $this->deliveryModel->updateOrderStatus($orderId, $status);
    }

    
    public function getDeliveryStatusCounts() {
        return $this->deliveryModel->getDeliveryStatusCounts();
    }

    
    public function getDeliveryPersonnel() {
        return $this->deliveryModel->getDeliveryPersonnel();
    }

    
    public function assignDelivery($deliveryId, $personnelId) {
        return $this->deliveryModel->assignDelivery($deliveryId, $personnelId);
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
            
            $availableOptions = $allStatuses;
        }

        return $availableOptions;
    }

    
    public function getDeliveryPersonnelById($id) {
        return $this->deliveryModel->getDeliveryPersonnelById($id);
    }

    
    public function createDeliveryPersonnel($data) {
        return $this->deliveryModel->createDeliveryPersonnel($data);
    }

    
    public function updateDeliveryPersonnel($id, $data) {
        return $this->deliveryModel->updateDeliveryPersonnel($id, $data);
    }

    
    public function deleteDeliveryPersonnel($id) {
        return $this->deliveryModel->deleteDeliveryPersonnel($id);
    }

    
    public function emailExists($email, $excludeId = null) {
        return $this->deliveryModel->emailExists($email, $excludeId);
    }

    
    public function getActiveOrdersCount($personnelId) {
        return $this->deliveryModel->getActiveOrdersCount($personnelId);
    }

    
    public function getDeliveriesByPersonnelId($personnelId) {
        return $this->deliveryModel->getDeliveriesByPersonnelId($personnelId);
    }

    
    public function getDeliveryStatsByPersonnelId($personnelId) {
        return $this->deliveryModel->getDeliveryStatsByPersonnelId($personnelId);
    }
}
?>
