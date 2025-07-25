<?php
require_once __DIR__ . '/../models/AdminCustomerModel.php';

class AdminCustomerController {
    private $customerModel;

    public function __construct($db) {
        $this->customerModel = new AdminCustomerModel($db);
    }

    
    public function getCustomers() {
        return $this->customerModel->getCustomers();
    }

    
    public function getCustomerById($id) {
        return $this->customerModel->getCustomerById($id);
    }

    
    public function getCustomerOrders($userId) {
        return $this->customerModel->getCustomerOrders($userId);
    }

    
    public function getCustomerWishlist($userId) {
        return $this->customerModel->getCustomerWishlist($userId);
    }

    
    public function getCustomerCart($userId) {
        return $this->customerModel->getCustomerCart($userId);
    }

    
    public function updateCustomer($id, $data) {
        return $this->customerModel->updateCustomer($id, $data);
    }

    
    public function formatDate($date) {
        return date('M d, Y', strtotime($date));
    }

    
    public function formatCurrency($amount) {
        return number_format($amount, 2) . 'DH';
    }

    
    public function getCustomerTypeLabel($isDelivery) {
        return $isDelivery ? 'Delivery Personnel' : 'Customer';
    }

    
    public function getCustomerTypeBadgeClass($isDelivery) {
        return $isDelivery ? 'badge-info' : 'badge-primary';
    }

    
    public function getOrderStatusBadgeClass($status) {
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

    
    public function deleteCustomer($id) {
        return $this->customerModel->deleteCustomer($id);
    }
}
?>
