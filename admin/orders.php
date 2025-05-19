<?php
// Include configuration
require_once '../config/config.php';
require_once '../config/Database.php';
require_once './controllers/AdminOrderController.php';
require_once './controllers/AdminDeliveryController.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('/views/auth/login.php');
}

// Set page title
$pageTitle = 'Orders';
$customCss = 'orders.css';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Create controllers
$orderController = new AdminOrderController($db);
$deliveryController = new AdminDeliveryController($db);

// Initialize filters array
$filters = [];

// Check if status filter is set
if (isset($_GET['status']) && !empty($_GET['status'])) {
    $filters['status'] = $_GET['status'];
}

// Get filtered orders
$orders = $orderController->getOrders($filters);

// Get order status counts
$statusCounts = $orderController->getOrderStatusCounts();

// Get delivery personnel for dropdown
$deliveryPersonnel = $deliveryController->getDeliveryPersonnel();

// Include header
include_once './includes/header.php';
?>

<!-- Orders Content -->

<?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo $_SESSION['success_message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo $_SESSION['error_message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['error_message']); ?>
<?php endif; ?>

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
                                            <div class="d-flex">
                                                <a href="<?php echo url('/admin/order-details.php?id=' . $order['id']); ?>" class="btn btn-sm btn-outline-primary me-2">
                                                    <i class="fas fa-eye"></i> View
                                                </a>

                                                <?php if ($order['status'] === 'pending'): ?>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="assignDropdown<?php echo $order['id']; ?>" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="fas fa-truck"></i>
                                                    </button>
                                                    <ul class="dropdown-menu" aria-labelledby="assignDropdown<?php echo $order['id']; ?>">
                                                        <li><h6 class="dropdown-header">Assign to Delivery Personnel</h6></li>
                                                        <?php if (empty($deliveryPersonnel)): ?>
                                                            <li><span class="dropdown-item text-muted">No delivery personnel available</span></li>
                                                        <?php else: ?>
                                                            <?php foreach ($deliveryPersonnel as $personnel): ?>
                                                                <li>
                                                                    <form action="<?php echo url('/admin/controllers/assign-delivery.php'); ?>" method="post">
                                                                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                                        <input type="hidden" name="personnel_id" value="<?php echo $personnel['id']; ?>">
                                                                        <button type="submit" class="dropdown-item">
                                                                            <?php echo htmlspecialchars($personnel['first_name'] . ' ' . $personnel['last_name']); ?>
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </ul>
                                                </div>
                                                <?php endif; ?>
                                            </div>
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

