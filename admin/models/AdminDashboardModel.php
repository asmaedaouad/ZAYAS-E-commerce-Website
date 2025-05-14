<?php
class AdminDashboardModel {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Get total sales (only count delivered orders)
    public function getTotalSales() {
        $query = "SELECT SUM(total_amount) as total_sales 
                  FROM orders 
                  WHERE status = 'delivered'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $result = $stmt->fetch();
        return $result['total_sales'] ? $result['total_sales'] : 0;
    }
    
    // Get total orders
    public function getTotalOrders() {
        $query = "SELECT COUNT(*) as total_orders FROM orders";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $result = $stmt->fetch();
        return $result['total_orders'];
    }
    
    // Get total customers (non-admin users)
    public function getTotalCustomers() {
        $query = "SELECT COUNT(*) as total_customers 
                  FROM users 
                  WHERE is_admin = 0 AND is_delivery = 0";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $result = $stmt->fetch();
        return $result['total_customers'];
    }
    
    // Get total products
    public function getTotalProducts() {
        $query = "SELECT COUNT(*) as total_products FROM products";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $result = $stmt->fetch();
        return $result['total_products'];
    }
    
    // Get recent orders (last 3)
    public function getRecentOrders($limit = 3) {
        $query = "SELECT o.*, u.first_name, u.last_name 
                  FROM orders o 
                  JOIN users u ON o.user_id = u.id 
                  ORDER BY o.created_at DESC 
                  LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    // Get monthly sales data for chart
    public function getMonthlySales($year = null) {
        // If year not provided, use current year
        if (!$year) {
            $year = date('Y');
        }
        
        $query = "SELECT MONTH(created_at) as month, SUM(total_amount) as total 
                  FROM orders 
                  WHERE YEAR(created_at) = :year 
                  AND status = 'delivered'
                  GROUP BY MONTH(created_at) 
                  ORDER BY month";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':year', $year);
        $stmt->execute();
        
        // Initialize array with all months
        $monthlySales = array_fill(1, 12, 0);
        
        // Fill in actual data
        while ($row = $stmt->fetch()) {
            $monthlySales[$row['month']] = (float)$row['total'];
        }
        
        return $monthlySales;
    }
    
    // Get monthly orders data for chart
    public function getMonthlyOrders($year = null) {
        // If year not provided, use current year
        if (!$year) {
            $year = date('Y');
        }
        
        $query = "SELECT MONTH(created_at) as month, COUNT(*) as total 
                  FROM orders 
                  WHERE YEAR(created_at) = :year 
                  GROUP BY MONTH(created_at) 
                  ORDER BY month";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':year', $year);
        $stmt->execute();
        
        // Initialize array with all months
        $monthlyOrders = array_fill(1, 12, 0);
        
        // Fill in actual data
        while ($row = $stmt->fetch()) {
            $monthlyOrders[$row['month']] = (int)$row['total'];
        }
        
        return $monthlyOrders;
    }
    
    // Get product type distribution for chart
    public function getProductTypeDistribution() {
        $query = "SELECT type, COUNT(*) as count 
                  FROM products 
                  GROUP BY type 
                  ORDER BY count DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $types = [];
        $counts = [];
        
        while ($row = $stmt->fetch()) {
            $types[] = ucfirst($row['type']);
            $counts[] = (int)$row['count'];
        }
        
        return [
            'types' => $types,
            'counts' => $counts
        ];
    }
    
    // Get order status distribution for chart
    public function getOrderStatusDistribution() {
        $query = "SELECT status, COUNT(*) as count 
                  FROM orders 
                  GROUP BY status 
                  ORDER BY count DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $statuses = [];
        $counts = [];
        
        while ($row = $stmt->fetch()) {
            $statuses[] = ucfirst($row['status']);
            $counts[] = (int)$row['count'];
        }
        
        return [
            'statuses' => $statuses,
            'counts' => $counts
        ];
    }
}
?>
