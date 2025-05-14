<?php
require_once __DIR__ . '/../models/AdminDashboardModel.php';

class AdminDashboardController {
    private $dashboardModel;
    
    public function __construct($db) {
        $this->dashboardModel = new AdminDashboardModel($db);
    }
    
    // Get dashboard data
    public function getDashboardData() {
        // Get statistics
        $totalSales = $this->dashboardModel->getTotalSales();
        $totalOrders = $this->dashboardModel->getTotalOrders();
        $totalCustomers = $this->dashboardModel->getTotalCustomers();
        $totalProducts = $this->dashboardModel->getTotalProducts();
        
        // Get recent orders
        $recentOrders = $this->dashboardModel->getRecentOrders(3);
        
        // Get chart data
        $monthlySales = $this->dashboardModel->getMonthlySales();
        $monthlyOrders = $this->dashboardModel->getMonthlyOrders();
        $productTypeDistribution = $this->dashboardModel->getProductTypeDistribution();
        $orderStatusDistribution = $this->dashboardModel->getOrderStatusDistribution();
        
        // Return all data
        return [
            'total_sales' => $totalSales,
            'total_orders' => $totalOrders,
            'total_customers' => $totalCustomers,
            'total_products' => $totalProducts,
            'recent_orders' => $recentOrders,
            'monthly_sales' => $monthlySales,
            'monthly_orders' => $monthlyOrders,
            'product_type_distribution' => $productTypeDistribution,
            'order_status_distribution' => $orderStatusDistribution
        ];
    }
    
    // Format currency
    public function formatCurrency($amount) {
        return '$' . number_format($amount, 2);
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
}
?>
