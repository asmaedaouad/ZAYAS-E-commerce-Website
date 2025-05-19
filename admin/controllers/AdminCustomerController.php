<?php
require_once __DIR__ . '/../models/AdminCustomerModel.php';

class AdminCustomerController {
    private $customerModel;

    public function __construct($db) {
        $this->customerModel = new AdminCustomerModel($db);
    }

    // Get all customers
    public function getCustomers() {
        return $this->customerModel->getCustomers();
    }

    // Get customer by ID
    public function getCustomerById($id) {
        return $this->customerModel->getCustomerById($id);
    }

    // Get customer orders
    public function getCustomerOrders($userId) {
        return $this->customerModel->getCustomerOrders($userId);
    }

    // Get customer wishlist
    public function getCustomerWishlist($userId) {
        return $this->customerModel->getCustomerWishlist($userId);
    }

    // Get customer cart
    public function getCustomerCart($userId) {
        return $this->customerModel->getCustomerCart($userId);
    }

    // Update customer
    public function updateCustomer($id, $data) {
        return $this->customerModel->updateCustomer($id, $data);
    }

    // Format date
    public function formatDate($date) {
        return date('M d, Y', strtotime($date));
    }

    // Format currency
    public function formatCurrency($amount) {
        return number_format($amount, 2) . 'DH';
    }

    // Get customer type label
    public function getCustomerTypeLabel($isDelivery) {
        return $isDelivery ? 'Delivery Personnel' : 'Customer';
    }

    // Get customer type badge class
    public function getCustomerTypeBadgeClass($isDelivery) {
        return $isDelivery ? 'badge-info' : 'badge-primary';
    }

    // Get order status badge class
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

    // Format status for display
    public function formatStatus($status) {
        return ucfirst(str_replace('_', ' ', $status));
    }
}
?>
