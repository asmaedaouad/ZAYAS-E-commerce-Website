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

    // Get daily sales data for chart (last 30 days)
    public function getDailySales($days = 30) {
        // Calculate the date 30 days ago
        $startDate = date('Y-m-d', strtotime("-{$days} days"));

        $query = "SELECT DATE(created_at) as date, SUM(total_amount) as total
                  FROM orders
                  WHERE created_at >= :start_date
                  AND status = 'delivered'
                  GROUP BY DATE(created_at)
                  ORDER BY date";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->execute();

        $dates = [];
        $sales = [];

        // Get all dates in the last 30 days
        $allDates = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $allDates[date('Y-m-d', strtotime("-{$i} days"))] = 0;
        }

        // Fill in actual data
        while ($row = $stmt->fetch()) {
            $allDates[$row['date']] = (float)$row['total'];
        }

        // Format dates for display and prepare data array
        foreach ($allDates as $date => $total) {
            $dates[] = date('d M', strtotime($date));
            $sales[] = $total;
        }

        return [
            'dates' => $dates,
            'sales' => $sales
        ];
    }

    // Get daily orders data for chart (last 30 days)
    public function getDailyOrders($days = 30) {
        // Calculate the date 30 days ago
        $startDate = date('Y-m-d', strtotime("-{$days} days"));

        $query = "SELECT DATE(created_at) as date, COUNT(*) as total
                  FROM orders
                  WHERE created_at >= :start_date
                  GROUP BY DATE(created_at)
                  ORDER BY date";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->execute();

        // Get all dates in the last 30 days
        $allDates = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $allDates[date('Y-m-d', strtotime("-{$i} days"))] = 0;
        }

        // Fill in actual data
        while ($row = $stmt->fetch()) {
            $allDates[$row['date']] = (int)$row['total'];
        }

        // Format dates for display and prepare data array
        $dates = [];
        $orders = [];
        foreach ($allDates as $date => $total) {
            $dates[] = date('d M', strtotime($date));
            $orders[] = $total;
        }

        return [
            'dates' => $dates,
            'orders' => $orders
        ];
    }

    // Get daily customers data for chart (last 30 days)
    public function getDailyCustomers($days = 30) {
        // Calculate the date 30 days ago
        $startDate = date('Y-m-d', strtotime("-{$days} days"));

        $query = "SELECT DATE(created_at) as date, COUNT(*) as total
                  FROM users
                  WHERE created_at >= :start_date
                  AND is_admin = 0 AND is_delivery = 0
                  GROUP BY DATE(created_at)
                  ORDER BY date";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->execute();

        // Get all dates in the last 30 days
        $allDates = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $allDates[date('Y-m-d', strtotime("-{$i} days"))] = 0;
        }

        // Fill in actual data
        while ($row = $stmt->fetch()) {
            $allDates[$row['date']] = (int)$row['total'];
        }

        // Format dates for display and prepare data array
        $dates = [];
        $customers = [];
        foreach ($allDates as $date => $total) {
            $dates[] = date('d M', strtotime($date));
            $customers[] = $total;
        }

        return [
            'dates' => $dates,
            'customers' => $customers
        ];
    }

    // Get daily products data for chart (last 30 days)
    public function getDailyProducts($days = 30) {
        // Calculate the date 30 days ago
        $startDate = date('Y-m-d', strtotime("-{$days} days"));

        $query = "SELECT DATE(created_at) as date, COUNT(*) as total
                  FROM products
                  WHERE created_at >= :start_date
                  GROUP BY DATE(created_at)
                  ORDER BY date";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->execute();

        // Get all dates in the last 30 days
        $allDates = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $allDates[date('Y-m-d', strtotime("-{$i} days"))] = 0;
        }

        // Fill in actual data
        while ($row = $stmt->fetch()) {
            $allDates[$row['date']] = (int)$row['total'];
        }

        // Format dates for display and prepare data array
        $dates = [];
        $products = [];
        foreach ($allDates as $date => $total) {
            $dates[] = date('d M', strtotime($date));
            $products[] = $total;
        }

        return [
            'dates' => $dates,
            'products' => $products
        ];
    }

    // Get monthly customers data for chart
    public function getMonthlyCustomers($year = null) {
        // If year not provided, use current year
        if (!$year) {
            $year = date('Y');
        }

        $query = "SELECT MONTH(created_at) as month, COUNT(*) as total
                  FROM users
                  WHERE YEAR(created_at) = :year
                  AND is_admin = 0 AND is_delivery = 0
                  GROUP BY MONTH(created_at)
                  ORDER BY month";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':year', $year);
        $stmt->execute();

        // Initialize array with all months
        $monthlyCustomers = array_fill(1, 12, 0);

        // Fill in actual data
        while ($row = $stmt->fetch()) {
            $monthlyCustomers[$row['month']] = (int)$row['total'];
        }

        return $monthlyCustomers;
    }

    // Get monthly products data for chart
    public function getMonthlyProducts($year = null) {
        // If year not provided, use current year
        if (!$year) {
            $year = date('Y');
        }

        $query = "SELECT MONTH(created_at) as month, COUNT(*) as total
                  FROM products
                  WHERE YEAR(created_at) = :year
                  GROUP BY MONTH(created_at)
                  ORDER BY month";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':year', $year);
        $stmt->execute();

        // Initialize array with all months
        $monthlyProducts = array_fill(1, 12, 0);

        // Fill in actual data
        while ($row = $stmt->fetch()) {
            $monthlyProducts[$row['month']] = (int)$row['total'];
        }

        return $monthlyProducts;
    }

    // Get weekly sales data
    public function getWeeklySales($weeks = 4) {
        // Calculate the date for the start of the period
        $startDate = date('Y-m-d', strtotime("-" . ($weeks * 7) . " days"));

        $query = "SELECT YEARWEEK(created_at, 1) as week, SUM(total_amount) as total
                  FROM orders
                  WHERE created_at >= :start_date
                  AND status = 'delivered'
                  GROUP BY YEARWEEK(created_at, 1)
                  ORDER BY week";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->execute();

        // Get data
        $weeklySales = [];
        $weekLabels = [];

        // Generate week labels
        for ($i = $weeks - 1; $i >= 0; $i--) {
            $weekStart = date('Y-m-d', strtotime("-" . ($i * 7) . " days"));
            $weekLabels[] = 'Week ' . ($weeks - $i);
            $weeklySales[] = 0; // Initialize with 0
        }

        // Fill in actual data
        $weekIndex = 0;
        while ($row = $stmt->fetch()) {
            if ($weekIndex < count($weeklySales)) {
                $weeklySales[$weekIndex] = (float)$row['total'];
            }
            $weekIndex++;
        }

        return [
            'labels' => $weekLabels,
            'data' => $weeklySales
        ];
    }

    // Get weekly orders data
    public function getWeeklyOrders($weeks = 4) {
        // Calculate the date for the start of the period
        $startDate = date('Y-m-d', strtotime("-" . ($weeks * 7) . " days"));

        $query = "SELECT YEARWEEK(created_at, 1) as week, COUNT(*) as total
                  FROM orders
                  WHERE created_at >= :start_date
                  GROUP BY YEARWEEK(created_at, 1)
                  ORDER BY week";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->execute();

        // Get data
        $weeklyOrders = [];
        $weekLabels = [];

        // Generate week labels
        for ($i = $weeks - 1; $i >= 0; $i--) {
            $weekStart = date('Y-m-d', strtotime("-" . ($i * 7) . " days"));
            $weekLabels[] = 'Week ' . ($weeks - $i);
            $weeklyOrders[] = 0; // Initialize with 0
        }

        // Fill in actual data
        $weekIndex = 0;
        while ($row = $stmt->fetch()) {
            if ($weekIndex < count($weeklyOrders)) {
                $weeklyOrders[$weekIndex] = (int)$row['total'];
            }
            $weekIndex++;
        }

        return [
            'labels' => $weekLabels,
            'data' => $weeklyOrders
        ];
    }

    // Get weekly customers data
    public function getWeeklyCustomers($weeks = 4) {
        // Calculate the date for the start of the period
        $startDate = date('Y-m-d', strtotime("-" . ($weeks * 7) . " days"));

        $query = "SELECT YEARWEEK(created_at, 1) as week, COUNT(*) as total
                  FROM users
                  WHERE created_at >= :start_date
                  AND is_admin = 0 AND is_delivery = 0
                  GROUP BY YEARWEEK(created_at, 1)
                  ORDER BY week";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->execute();

        // Get data
        $weeklyCustomers = [];
        $weekLabels = [];

        // Generate week labels
        for ($i = $weeks - 1; $i >= 0; $i--) {
            $weekStart = date('Y-m-d', strtotime("-" . ($i * 7) . " days"));
            $weekLabels[] = 'Week ' . ($weeks - $i);
            $weeklyCustomers[] = 0; // Initialize with 0
        }

        // Fill in actual data
        $weekIndex = 0;
        while ($row = $stmt->fetch()) {
            if ($weekIndex < count($weeklyCustomers)) {
                $weeklyCustomers[$weekIndex] = (int)$row['total'];
            }
            $weekIndex++;
        }

        return [
            'labels' => $weekLabels,
            'data' => $weeklyCustomers
        ];
    }

    // Get weekly products data
    public function getWeeklyProducts($weeks = 4) {
        // Calculate the date for the start of the period
        $startDate = date('Y-m-d', strtotime("-" . ($weeks * 7) . " days"));

        $query = "SELECT YEARWEEK(created_at, 1) as week, COUNT(*) as total
                  FROM products
                  WHERE created_at >= :start_date
                  GROUP BY YEARWEEK(created_at, 1)
                  ORDER BY week";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->execute();

        // Get data
        $weeklyProducts = [];
        $weekLabels = [];

        // Generate week labels
        for ($i = $weeks - 1; $i >= 0; $i--) {
            $weekStart = date('Y-m-d', strtotime("-" . ($i * 7) . " days"));
            $weekLabels[] = 'Week ' . ($weeks - $i);
            $weeklyProducts[] = 0; // Initialize with 0
        }

        // Fill in actual data
        $weekIndex = 0;
        while ($row = $stmt->fetch()) {
            if ($weekIndex < count($weeklyProducts)) {
                $weeklyProducts[$weekIndex] = (int)$row['total'];
            }
            $weekIndex++;
        }

        return [
            'labels' => $weekLabels,
            'data' => $weeklyProducts
        ];
    }

    // Get yearly sales data
    public function getYearlySales($years = 5) {
        $currentYear = date('Y');
        $startYear = $currentYear - $years + 1;

        $query = "SELECT YEAR(created_at) as year, SUM(total_amount) as total
                  FROM orders
                  WHERE YEAR(created_at) >= :start_year
                  AND status = 'delivered'
                  GROUP BY YEAR(created_at)
                  ORDER BY year";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':start_year', $startYear);
        $stmt->execute();

        // Initialize arrays
        $yearlyData = [];
        $yearLabels = [];

        // Generate year labels and initialize data
        for ($i = 0; $i < $years; $i++) {
            $year = $startYear + $i;
            $yearLabels[] = (string)$year;
            $yearlyData[$year] = 0;
        }

        // Fill in actual data
        while ($row = $stmt->fetch()) {
            $yearlyData[$row['year']] = (float)$row['total'];
        }

        return [
            'labels' => $yearLabels,
            'data' => array_values($yearlyData)
        ];
    }

    // Get yearly orders data
    public function getYearlyOrders($years = 5) {
        $currentYear = date('Y');
        $startYear = $currentYear - $years + 1;

        $query = "SELECT YEAR(created_at) as year, COUNT(*) as total
                  FROM orders
                  WHERE YEAR(created_at) >= :start_year
                  GROUP BY YEAR(created_at)
                  ORDER BY year";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':start_year', $startYear);
        $stmt->execute();

        // Initialize arrays
        $yearlyData = [];
        $yearLabels = [];

        // Generate year labels and initialize data
        for ($i = 0; $i < $years; $i++) {
            $year = $startYear + $i;
            $yearLabels[] = (string)$year;
            $yearlyData[$year] = 0;
        }

        // Fill in actual data
        while ($row = $stmt->fetch()) {
            $yearlyData[$row['year']] = (int)$row['total'];
        }

        return [
            'labels' => $yearLabels,
            'data' => array_values($yearlyData)
        ];
    }

    // Get yearly customers data
    public function getYearlyCustomers($years = 5) {
        $currentYear = date('Y');
        $startYear = $currentYear - $years + 1;

        $query = "SELECT YEAR(created_at) as year, COUNT(*) as total
                  FROM users
                  WHERE YEAR(created_at) >= :start_year
                  AND is_admin = 0 AND is_delivery = 0
                  GROUP BY YEAR(created_at)
                  ORDER BY year";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':start_year', $startYear);
        $stmt->execute();

        // Initialize arrays
        $yearlyData = [];
        $yearLabels = [];

        // Generate year labels and initialize data
        for ($i = 0; $i < $years; $i++) {
            $year = $startYear + $i;
            $yearLabels[] = (string)$year;
            $yearlyData[$year] = 0;
        }

        // Fill in actual data
        while ($row = $stmt->fetch()) {
            $yearlyData[$row['year']] = (int)$row['total'];
        }

        return [
            'labels' => $yearLabels,
            'data' => array_values($yearlyData)
        ];
    }

    // Get yearly products data
    public function getYearlyProducts($years = 5) {
        $currentYear = date('Y');
        $startYear = $currentYear - $years + 1;

        $query = "SELECT YEAR(created_at) as year, COUNT(*) as total
                  FROM products
                  WHERE YEAR(created_at) >= :start_year
                  GROUP BY YEAR(created_at)
                  ORDER BY year";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':start_year', $startYear);
        $stmt->execute();

        // Initialize arrays
        $yearlyData = [];
        $yearLabels = [];

        // Generate year labels and initialize data
        for ($i = 0; $i < $years; $i++) {
            $year = $startYear + $i;
            $yearLabels[] = (string)$year;
            $yearlyData[$year] = 0;
        }

        // Fill in actual data
        while ($row = $stmt->fetch()) {
            $yearlyData[$row['year']] = (int)$row['total'];
        }

        return [
            'labels' => $yearLabels,
            'data' => array_values($yearlyData)
        ];
    }

    // Get daily orders data by status (last 30 days)
    public function getDailyOrdersByStatus($days = 30) {
        // Calculate the date 30 days ago
        $startDate = date('Y-m-d', strtotime("-{$days} days"));

        $query = "SELECT DATE(created_at) as date, status, COUNT(*) as total
                  FROM orders
                  WHERE created_at >= :start_date
                  GROUP BY DATE(created_at), status
                  ORDER BY date, status";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->execute();

        // Get all dates in the last 30 days
        $allDates = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $allDates[date('Y-m-d', strtotime("-{$i} days"))] = [
                'pending' => 0,
                'assigned' => 0,
                'in_transit' => 0,
                'delivered' => 0,
                'cancelled' => 0,
                'returned' => 0
            ];
        }

        // Fill in actual data
        while ($row = $stmt->fetch()) {
            if (isset($allDates[$row['date']][$row['status']])) {
                $allDates[$row['date']][$row['status']] = (int)$row['total'];
            }
        }

        // Format dates for display and prepare data arrays
        $dates = [];
        $pending = [];
        $assigned = [];
        $inTransit = [];
        $delivered = [];
        $cancelled = [];
        $returned = [];

        foreach ($allDates as $date => $statusCounts) {
            $dates[] = date('d M', strtotime($date));
            $pending[] = $statusCounts['pending'];
            $assigned[] = $statusCounts['assigned'];
            $inTransit[] = $statusCounts['in_transit'];
            $delivered[] = $statusCounts['delivered'];
            $cancelled[] = $statusCounts['cancelled'];
            $returned[] = $statusCounts['returned'];
        }

        return [
            'dates' => $dates,
            'pending' => $pending,
            'assigned' => $assigned,
            'in_transit' => $inTransit,
            'delivered' => $delivered,
            'cancelled' => $cancelled,
            'returned' => $returned
        ];
    }

    // Get monthly orders data by status
    public function getMonthlyOrdersByStatus($year = null) {
        // If year not provided, use current year
        if (!$year) {
            $year = date('Y');
        }

        $query = "SELECT MONTH(created_at) as month, status, COUNT(*) as total
                  FROM orders
                  WHERE YEAR(created_at) = :year
                  GROUP BY MONTH(created_at), status
                  ORDER BY month, status";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':year', $year);
        $stmt->execute();

        // Initialize arrays with all months
        $months = range(1, 12);
        $pending = array_fill(0, 12, 0);
        $assigned = array_fill(0, 12, 0);
        $inTransit = array_fill(0, 12, 0);
        $delivered = array_fill(0, 12, 0);
        $cancelled = array_fill(0, 12, 0);
        $returned = array_fill(0, 12, 0);

        // Fill in actual data
        while ($row = $stmt->fetch()) {
            $monthIndex = $row['month'] - 1; // Convert to 0-based index

            switch ($row['status']) {
                case 'pending':
                    $pending[$monthIndex] = (int)$row['total'];
                    break;
                case 'assigned':
                    $assigned[$monthIndex] = (int)$row['total'];
                    break;
                case 'in_transit':
                    $inTransit[$monthIndex] = (int)$row['total'];
                    break;
                case 'delivered':
                    $delivered[$monthIndex] = (int)$row['total'];
                    break;
                case 'cancelled':
                    $cancelled[$monthIndex] = (int)$row['total'];
                    break;
                case 'returned':
                    $returned[$monthIndex] = (int)$row['total'];
                    break;
            }
        }

        // Convert month numbers to month names
        $monthNames = [];
        foreach ($months as $month) {
            $monthNames[] = date('M', mktime(0, 0, 0, $month, 1));
        }

        return [
            'months' => $monthNames,
            'pending' => $pending,
            'assigned' => $assigned,
            'in_transit' => $inTransit,
            'delivered' => $delivered,
            'cancelled' => $cancelled,
            'returned' => $returned
        ];
    }

    // Get weekly orders data by status
    public function getWeeklyOrdersByStatus($weeks = 4) {
        // Calculate the date for the start of the period
        $startDate = date('Y-m-d', strtotime("-" . ($weeks * 7) . " days"));

        $query = "SELECT YEARWEEK(created_at, 1) as week, status, COUNT(*) as total
                  FROM orders
                  WHERE created_at >= :start_date
                  GROUP BY YEARWEEK(created_at, 1), status
                  ORDER BY week, status";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->execute();

        // Get data
        $weekLabels = [];
        $weekData = [];

        // Generate week labels and initialize data structure
        for ($i = $weeks - 1; $i >= 0; $i--) {
            $weekStart = date('Y-m-d', strtotime("-" . ($i * 7) . " days"));
            $weekLabels[] = 'Week ' . ($weeks - $i);
            $weekData[] = [
                'pending' => 0,
                'assigned' => 0,
                'in_transit' => 0,
                'delivered' => 0,
                'cancelled' => 0,
                'returned' => 0
            ];
        }

        // Get all weeks in the period
        $weekNumbers = [];
        for ($i = $weeks - 1; $i >= 0; $i--) {
            $weekDate = date('Y-m-d', strtotime("-" . ($i * 7) . " days"));
            $weekNumbers[] = date('YW', strtotime($weekDate));
        }

        // Fill in actual data
        while ($row = $stmt->fetch()) {
            $weekIndex = array_search($row['week'], $weekNumbers);
            if ($weekIndex !== false && isset($weekData[$weekIndex][$row['status']])) {
                $weekData[$weekIndex][$row['status']] = (int)$row['total'];
            }
        }

        // Prepare the return data
        $pending = [];
        $assigned = [];
        $inTransit = [];
        $delivered = [];
        $cancelled = [];
        $returned = [];

        foreach ($weekData as $data) {
            $pending[] = $data['pending'];
            $assigned[] = $data['assigned'];
            $inTransit[] = $data['in_transit'];
            $delivered[] = $data['delivered'];
            $cancelled[] = $data['cancelled'];
            $returned[] = $data['returned'];
        }

        return [
            'labels' => $weekLabels,
            'pending' => $pending,
            'assigned' => $assigned,
            'in_transit' => $inTransit,
            'delivered' => $delivered,
            'cancelled' => $cancelled,
            'returned' => $returned
        ];
    }

    // Get yearly orders data by status
    public function getYearlyOrdersByStatus($years = 5) {
        $currentYear = date('Y');
        $startYear = $currentYear - $years + 1;

        $query = "SELECT YEAR(created_at) as year, status, COUNT(*) as total
                  FROM orders
                  WHERE YEAR(created_at) >= :start_year
                  GROUP BY YEAR(created_at), status
                  ORDER BY year, status";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':start_year', $startYear);
        $stmt->execute();

        // Initialize arrays
        $yearLabels = [];
        $yearData = [];

        // Generate year labels and initialize data
        for ($i = 0; $i < $years; $i++) {
            $year = $startYear + $i;
            $yearLabels[] = (string)$year;
            $yearData[$year] = [
                'pending' => 0,
                'assigned' => 0,
                'in_transit' => 0,
                'delivered' => 0,
                'cancelled' => 0,
                'returned' => 0
            ];
        }

        // Fill in actual data
        while ($row = $stmt->fetch()) {
            if (isset($yearData[$row['year']][$row['status']])) {
                $yearData[$row['year']][$row['status']] = (int)$row['total'];
            }
        }

        // Prepare the return data
        $pending = [];
        $assigned = [];
        $inTransit = [];
        $delivered = [];
        $cancelled = [];
        $returned = [];

        foreach ($yearData as $data) {
            $pending[] = $data['pending'];
            $assigned[] = $data['assigned'];
            $inTransit[] = $data['in_transit'];
            $delivered[] = $data['delivered'];
            $cancelled[] = $data['cancelled'];
            $returned[] = $data['returned'];
        }

        return [
            'labels' => $yearLabels,
            'pending' => $pending,
            'assigned' => $assigned,
            'in_transit' => $inTransit,
            'delivered' => $delivered,
            'cancelled' => $cancelled,
            'returned' => $returned
        ];
    }
}
?>
