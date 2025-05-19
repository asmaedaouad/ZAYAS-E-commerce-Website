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
        $productTypeDistribution = $this->dashboardModel->getProductTypeDistribution();
        $orderStatusDistribution = $this->dashboardModel->getOrderStatusDistribution();

        // Get monthly data
        $monthlySales = $this->dashboardModel->getMonthlySales();
        $monthlyOrders = $this->dashboardModel->getMonthlyOrders();
        $monthlyCustomers = $this->dashboardModel->getMonthlyCustomers();
        $monthlyProducts = $this->dashboardModel->getMonthlyProducts();

        // Get daily data
        $dailySales = $this->dashboardModel->getDailySales();
        $dailyOrders = $this->dashboardModel->getDailyOrders();
        $dailyCustomers = $this->dashboardModel->getDailyCustomers();
        $dailyProducts = $this->dashboardModel->getDailyProducts();

        // Get weekly data
        $weeklySales = $this->dashboardModel->getWeeklySales();
        $weeklyOrders = $this->dashboardModel->getWeeklyOrders();
        $weeklyCustomers = $this->dashboardModel->getWeeklyCustomers();
        $weeklyProducts = $this->dashboardModel->getWeeklyProducts();

        // Get yearly data
        $yearlySales = $this->dashboardModel->getYearlySales();
        $yearlyOrders = $this->dashboardModel->getYearlyOrders();
        $yearlyCustomers = $this->dashboardModel->getYearlyCustomers();
        $yearlyProducts = $this->dashboardModel->getYearlyProducts();

        // Get orders by status data
        $dailyOrdersByStatus = $this->dashboardModel->getDailyOrdersByStatus();
        $weeklyOrdersByStatus = $this->dashboardModel->getWeeklyOrdersByStatus();
        $monthlyOrdersByStatus = $this->dashboardModel->getMonthlyOrdersByStatus();
        $yearlyOrdersByStatus = $this->dashboardModel->getYearlyOrdersByStatus();

        // Return all data
        return [
            'total_sales' => $totalSales,
            'total_orders' => $totalOrders,
            'total_customers' => $totalCustomers,
            'total_products' => $totalProducts,
            'recent_orders' => $recentOrders,
            'product_type_distribution' => $productTypeDistribution,
            'order_status_distribution' => $orderStatusDistribution,

            // Monthly data
            'monthly_sales' => $monthlySales,
            'monthly_orders' => $monthlyOrders,
            'monthly_customers' => $monthlyCustomers,
            'monthly_products' => $monthlyProducts,

            // Daily data
            'daily_sales' => $dailySales,
            'daily_orders' => $dailyOrders,
            'daily_customers' => $dailyCustomers,
            'daily_products' => $dailyProducts,

            // Weekly data
            'weekly_sales' => $weeklySales,
            'weekly_orders' => $weeklyOrders,
            'weekly_customers' => $weeklyCustomers,
            'weekly_products' => $weeklyProducts,

            // Yearly data
            'yearly_sales' => $yearlySales,
            'yearly_orders' => $yearlyOrders,
            'yearly_customers' => $yearlyCustomers,
            'yearly_products' => $yearlyProducts,

            // Orders by status data
            'daily_orders_by_status' => $dailyOrdersByStatus,
            'weekly_orders_by_status' => $weeklyOrdersByStatus,
            'monthly_orders_by_status' => $monthlyOrdersByStatus,
            'yearly_orders_by_status' => $yearlyOrdersByStatus
        ];
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
            case 'processing':
                return 'badge-processing';
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
