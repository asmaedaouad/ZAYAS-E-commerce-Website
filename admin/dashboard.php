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
    <div class="col-lg-3 col-md-6">
        <div class="stat-card sales">
            <div class="stat-icon sales">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $dashboardController->formatCurrency($data['total_sales']); ?></h3>
                <p>Total Sales</p>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="stat-card orders">
            <div class="stat-icon orders">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $data['total_orders']; ?></h3>
                <p>Total Orders</p>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="stat-card customers">
            <div class="stat-icon customers">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $data['total_customers']; ?></h3>
                <p>Total Customers</p>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="stat-card products">
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

<div class="row equal-height-row">
    <!-- Custom Statistics Section -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-chart-line me-2"></i> Custom Statistics
            </div>
            <div class="card-body">
                <div class="statistics-controls">
                    <div class="statistics-control-item">
                        <label for="dataType">Data Type</label>
                        <div class="custom-select-container">
                            <select id="dataType" class="form-select">
                                <option value="Orders">Orders</option>
                                <option value="Sales">Sales</option>
                                <option value="Customers">Customers</option>
                                <option value="Products">Products</option>
                            </select>
                        </div>
                    </div>
                    <div class="statistics-control-item">
                        <label for="chartType">Chart Type</label>
                        <div class="custom-select-container">
                            <select id="chartType" class="form-select">
                                <option value="Bar Chart">Bar Chart</option>
                                <option value="Line Chart">Line Chart</option>
                                <option value="Pie Chart">Pie Chart</option>
                            </select>
                        </div>
                    </div>
                    <div class="statistics-control-item">
                        <label for="timePeriod">Time Period</label>
                        <div class="custom-select-container">
                            <select id="timePeriod" class="form-select">
                                <option value="Monthly">Monthly</option>
                                <option value="Weekly">Weekly</option>
                                <option value="Quarterly">Quarterly</option>
                                <option value="Yearly">Yearly</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="statistics-chart-container">
                    <h5 id="customChartTitle" class="statistics-chart-title">Orders by Monthly Period</h5>
                    <div class="chart-container">
                        <canvas id="customStatisticsChart"></canvas>
                    </div>
                    <div id="chartLegend" class="chart-legend"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Type Distribution -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-chart-pie me-2"></i> Product Distribution
            </div>
            <div class="card-body">
                <div class="product-distribution-container">
                    <div class="chart-container">
                        <canvas id="productTypeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Orders -->
<div class="row">
    <div class="col-12">
        <div class="card recent-orders-card">
            <div class="card-header">
                <i class="fas fa-shopping-cart me-2"></i> Recent Orders
                <a href="<?php echo url('/admin/orders.php'); ?>" class="view-all-link float-end">
                    View all <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ORDER #</th>
                                <th>CUSTOMER</th>
                                <th>DATE</th>
                                <th>AMOUNT</th>
                                <th>STATUS</th>
                                <th>ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($data['recent_orders'])): ?>
                                <tr>
                                    <td colspan="6" class="text-center">No recent orders</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($data['recent_orders'] as $order): ?>
                                    <tr>
                                        <td>#<?php echo $order['id']; ?></td>
                                        <td>
                                            <?php
                                                $initials = strtoupper(substr($order['first_name'], 0, 1) . substr($order['last_name'], 0, 1));
                                                $fullName = htmlspecialchars($order['first_name'] . ' ' . $order['last_name']);
                                            ?>
                                            <div class="d-flex align-items-center">
                                                <div class="me-2" style="width: 30px; height: 30px; background-color: #e8d9c5; color: #5d4037; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 600;"><?php echo $initials; ?></div>
                                                <?php echo $fullName; ?>
                                            </div>
                                        </td>
                                        <td><?php echo $dashboardController->formatDate($order['created_at']); ?></td>
                                        <td><?php echo $dashboardController->formatCurrency($order['total_amount']); ?></td>
                                        <td>
                                            <span class="badge <?php echo $dashboardController->getStatusBadgeClass($order['status']); ?>">
                                                <i class="fas fa-circle fa-sm"></i> <?php echo $dashboardController->formatStatus($order['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="<?php echo url('/admin/orders.php?id=' . $order['id']); ?>" class="action-btn" title="View Order">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?php echo url('/admin/orders.php?id=' . $order['id'] . '&edit=1'); ?>" class="action-btn" title="Edit Order">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
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
