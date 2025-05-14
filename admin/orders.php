<?php
// Include configuration
require_once '../config/config.php';
require_once '../config/Database.php';
require_once './controllers/AdminOrderController.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('/admin/login.php');
}

// Set page title
$pageTitle = 'Orders';
$customCss = 'orders.css';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Create order controller
$orderController = new AdminOrderController($db);

// Process filters
$filters = [];

if (isset($_GET['status']) && !empty($_GET['status'])) {
    $filters['status'] = $_GET['status'];
}

if (isset($_GET['date_from']) && !empty($_GET['date_from'])) {
    $filters['date_from'] = $_GET['date_from'];
}

if (isset($_GET['date_to']) && !empty($_GET['date_to'])) {
    $filters['date_to'] = $_GET['date_to'];
}

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $filters['search'] = $_GET['search'];
}

// Get orders with filters
$orders = $orderController->getOrders($filters);

// Get order status counts
$statusCounts = $orderController->getOrderStatusCounts();

// Include header
include_once './includes/header.php';
?>

<!-- Orders Content -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-filter me-2"></i> Filter Orders
            </div>
            <div class="card-body">
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label for="status" class="form-label">Order Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">All Statuses</option>
                            <option value="pending" <?php echo (isset($filters['status']) && $filters['status'] === 'pending') ? 'selected' : ''; ?>>Pending</option>
                            <option value="assigned" <?php echo (isset($filters['status']) && $filters['status'] === 'assigned') ? 'selected' : ''; ?>>Assigned</option>
                            <option value="in_transit" <?php echo (isset($filters['status']) && $filters['status'] === 'in_transit') ? 'selected' : ''; ?>>In Transit</option>
                            <option value="delivered" <?php echo (isset($filters['status']) && $filters['status'] === 'delivered') ? 'selected' : ''; ?>>Delivered</option>
                            <option value="cancelled" <?php echo (isset($filters['status']) && $filters['status'] === 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                            <option value="returned" <?php echo (isset($filters['status']) && $filters['status'] === 'returned') ? 'selected' : ''; ?>>Returned</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="date_from" class="form-label">Date From</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" value="<?php echo isset($filters['date_from']) ? htmlspecialchars($filters['date_from']) : ''; ?>">
                    </div>
                    
                    <div class="col-md-3">
                        <label for="date_to" class="form-label">Date To</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" value="<?php echo isset($filters['date_to']) ? htmlspecialchars($filters['date_to']) : ''; ?>">
                    </div>
                    
                    <div class="col-md-3">
                        <label for="search" class="form-label">Search Customer</label>
                        <input type="text" class="form-control" id="search" name="search" placeholder="Name or email..." value="<?php echo isset($filters['search']) ? htmlspecialchars($filters['search']) : ''; ?>">
                    </div>
                    
                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                        <a href="<?php echo url('/admin/orders.php'); ?>" class="btn btn-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="status-cards">
            <div class="status-card <?php echo empty($filters['status']) ? 'active' : ''; ?>">
                <a href="<?php echo url('/admin/orders.php'); ?>">
                    <div class="status-card-body">
                        <h3><?php echo array_sum($statusCounts); ?></h3>
                        <p>All Orders</p>
                    </div>
                </a>
            </div>
            
            <div class="status-card <?php echo (isset($filters['status']) && $filters['status'] === 'pending') ? 'active' : ''; ?>">
                <a href="<?php echo url('/admin/orders.php?status=pending'); ?>">
                    <div class="status-card-body">
                        <h3><?php echo isset($statusCounts['pending']) ? $statusCounts['pending'] : 0; ?></h3>
                        <p>Pending</p>
                    </div>
                </a>
            </div>
            
            <div class="status-card <?php echo (isset($filters['status']) && $filters['status'] === 'assigned') ? 'active' : ''; ?>">
                <a href="<?php echo url('/admin/orders.php?status=assigned'); ?>">
                    <div class="status-card-body">
                        <h3><?php echo isset($statusCounts['assigned']) ? $statusCounts['assigned'] : 0; ?></h3>
                        <p>Assigned</p>
                    </div>
                </a>
            </div>
            
            <div class="status-card <?php echo (isset($filters['status']) && $filters['status'] === 'in_transit') ? 'active' : ''; ?>">
                <a href="<?php echo url('/admin/orders.php?status=in_transit'); ?>">
                    <div class="status-card-body">
                        <h3><?php echo isset($statusCounts['in_transit']) ? $statusCounts['in_transit'] : 0; ?></h3>
                        <p>In Transit</p>
                    </div>
                </a>
            </div>
            
            <div class="status-card <?php echo (isset($filters['status']) && $filters['status'] === 'delivered') ? 'active' : ''; ?>">
                <a href="<?php echo url('/admin/orders.php?status=delivered'); ?>">
                    <div class="status-card-body">
                        <h3><?php echo isset($statusCounts['delivered']) ? $statusCounts['delivered'] : 0; ?></h3>
                        <p>Delivered</p>
                    </div>
                </a>
            </div>
            
            <div class="status-card <?php echo (isset($filters['status']) && $filters['status'] === 'cancelled') ? 'active' : ''; ?>">
                <a href="<?php echo url('/admin/orders.php?status=cancelled'); ?>">
                    <div class="status-card-body">
                        <h3><?php echo isset($statusCounts['cancelled']) ? $statusCounts['cancelled'] : 0; ?></h3>
                        <p>Cancelled</p>
                    </div>
                </a>
            </div>
            
            <div class="status-card <?php echo (isset($filters['status']) && $filters['status'] === 'returned') ? 'active' : ''; ?>">
                <a href="<?php echo url('/admin/orders.php?status=returned'); ?>">
                    <div class="status-card-body">
                        <h3><?php echo isset($statusCounts['returned']) ? $statusCounts['returned'] : 0; ?></h3>
                        <p>Returned</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-shopping-cart me-2"></i> Order List
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
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($orders)): ?>
                                <tr>
                                    <td colspan="6" class="text-center">No orders found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td>#<?php echo $order['id']; ?></td>
                                        <td><?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></td>
                                        <td><?php echo $orderController->formatCurrency($order['total_amount']); ?></td>
                                        <td>
                                            <span class="badge <?php echo $orderController->getStatusBadgeClass($order['status']); ?>">
                                                <?php echo $orderController->formatStatus($order['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo $orderController->formatDate($order['created_at']); ?></td>
                                        <td>
                                            <a href="<?php echo url('/admin/order-details.php?id=' . $order['id']); ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> View
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

<?php
// Include footer
include_once './includes/footer.php';
?>
