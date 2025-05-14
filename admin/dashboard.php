<?php
// Include configuration
require_once '../config/config.php';
require_once '../config/Database.php';
require_once './controllers/AdminDashboardController.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('/admin/login.php');
}

// Set page title
$pageTitle = 'Dashboard';
$customCss = 'dashboard.css';
$customJs = 'dashboard.js';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Create dashboard controller
$dashboardController = new AdminDashboardController($db);

// Get dashboard data
$data = $dashboardController->getDashboardData();

// Include header
include_once './includes/header.php';
?>

<!-- Dashboard Content -->
<div class="row">
    <!-- Statistics Cards -->
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon sales">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $dashboardController->formatCurrency($data['total_sales']); ?></h3>
                <p>Total Sales</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon orders">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $data['total_orders']; ?></h3>
                <p>Total Orders</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon customers">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $data['total_customers']; ?></h3>
                <p>Total Customers</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon products">
                <i class="fas fa-box"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $data['total_products']; ?></h3>
                <p>Total Products</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Orders -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-shopping-cart me-2"></i> Recent Orders
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($data['recent_orders'])): ?>
                                <tr>
                                    <td colspan="5" class="text-center">No recent orders</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($data['recent_orders'] as $order): ?>
                                    <tr>
                                        <td>#<?php echo $order['id']; ?></td>
                                        <td><?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></td>
                                        <td><?php echo $dashboardController->formatCurrency($order['total_amount']); ?></td>
                                        <td>
                                            <span class="badge <?php echo $dashboardController->getStatusBadgeClass($order['status']); ?>">
                                                <?php echo $dashboardController->formatStatus($order['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo $dashboardController->formatDate($order['created_at']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="text-end mt-3">
                    <a href="<?php echo url('/admin/orders.php'); ?>" class="btn btn-sm btn-outline-secondary">View All Orders</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Product Type Distribution -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-chart-pie me-2"></i> Product Distribution
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="productTypeChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Monthly Sales Chart -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-chart-line me-2"></i> Monthly Sales
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="monthlySalesChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Monthly Orders Chart -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-chart-bar me-2"></i> Monthly Orders
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="monthlyOrdersChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart Data -->
<script>
    // Chart data
    const monthlySalesData = <?php echo json_encode(array_values($data['monthly_sales'])); ?>;
    const monthlyOrdersData = <?php echo json_encode(array_values($data['monthly_orders'])); ?>;
    const productTypes = <?php echo json_encode($data['product_type_distribution']['types']); ?>;
    const productCounts = <?php echo json_encode($data['product_type_distribution']['counts']); ?>;
    const orderStatuses = <?php echo json_encode($data['order_status_distribution']['statuses']); ?>;
    const orderStatusCounts = <?php echo json_encode($data['order_status_distribution']['counts']); ?>;
</script>

<?php
// Include footer
include_once './includes/footer.php';
?>
