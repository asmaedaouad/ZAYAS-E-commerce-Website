<?php
// Include configuration
require_once '../config/config.php';
require_once '../config/Database.php';
require_once './controllers/AdminDeliveryController.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('/admin/login.php');
}

// Set page title
$pageTitle = 'Delivery Management';
$customCss = 'delivery.css';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Create delivery controller
$deliveryController = new AdminDeliveryController($db);

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

// Get deliveries with filters
$deliveries = $deliveryController->getDeliveries($filters);

// Get delivery status counts
$statusCounts = $deliveryController->getDeliveryStatusCounts();

// Include header
include_once './includes/header.php';
?>

<!-- Delivery Content -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-filter me-2"></i> Filter Deliveries
            </div>
            <div class="card-body">
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label for="status" class="form-label">Delivery Status</label>
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
                        <a href="<?php echo url('/admin/delivery.php'); ?>" class="btn btn-secondary">Reset</a>
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
                <a href="<?php echo url('/admin/delivery.php'); ?>">
                    <div class="status-card-body">
                        <h3><?php echo array_sum($statusCounts); ?></h3>
                        <p>All Deliveries</p>
                    </div>
                </a>
            </div>
            
            <div class="status-card <?php echo (isset($filters['status']) && $filters['status'] === 'pending') ? 'active' : ''; ?>">
                <a href="<?php echo url('/admin/delivery.php?status=pending'); ?>">
                    <div class="status-card-body">
                        <h3><?php echo isset($statusCounts['pending']) ? $statusCounts['pending'] : 0; ?></h3>
                        <p>Pending</p>
                    </div>
                </a>
            </div>
            
            <div class="status-card <?php echo (isset($filters['status']) && $filters['status'] === 'assigned') ? 'active' : ''; ?>">
                <a href="<?php echo url('/admin/delivery.php?status=assigned'); ?>">
                    <div class="status-card-body">
                        <h3><?php echo isset($statusCounts['assigned']) ? $statusCounts['assigned'] : 0; ?></h3>
                        <p>Assigned</p>
                    </div>
                </a>
            </div>
            
            <div class="status-card <?php echo (isset($filters['status']) && $filters['status'] === 'in_transit') ? 'active' : ''; ?>">
                <a href="<?php echo url('/admin/delivery.php?status=in_transit'); ?>">
                    <div class="status-card-body">
                        <h3><?php echo isset($statusCounts['in_transit']) ? $statusCounts['in_transit'] : 0; ?></h3>
                        <p>In Transit</p>
                    </div>
                </a>
            </div>
            
            <div class="status-card <?php echo (isset($filters['status']) && $filters['status'] === 'delivered') ? 'active' : ''; ?>">
                <a href="<?php echo url('/admin/delivery.php?status=delivered'); ?>">
                    <div class="status-card-body">
                        <h3><?php echo isset($statusCounts['delivered']) ? $statusCounts['delivered'] : 0; ?></h3>
                        <p>Delivered</p>
                    </div>
                </a>
            </div>
            
            <div class="status-card <?php echo (isset($filters['status']) && $filters['status'] === 'cancelled') ? 'active' : ''; ?>">
                <a href="<?php echo url('/admin/delivery.php?status=cancelled'); ?>">
                    <div class="status-card-body">
                        <h3><?php echo isset($statusCounts['cancelled']) ? $statusCounts['cancelled'] : 0; ?></h3>
                        <p>Cancelled</p>
                    </div>
                </a>
            </div>
            
            <div class="status-card <?php echo (isset($filters['status']) && $filters['status'] === 'returned') ? 'active' : ''; ?>">
                <a href="<?php echo url('/admin/delivery.php?status=returned'); ?>">
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
                <i class="fas fa-truck me-2"></i> Delivery List
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Address</th>
                                <th>Status</th>
                                <th>Order Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($deliveries)): ?>
                                <tr>
                                    <td colspan="7" class="text-center">No deliveries found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($deliveries as $delivery): ?>
                                    <tr>
                                        <td><?php echo $delivery['id']; ?></td>
                                        <td>#<?php echo $delivery['order_id']; ?></td>
                                        <td><?php echo htmlspecialchars($delivery['first_name'] . ' ' . $delivery['last_name']); ?></td>
                                        <td>
                                            <?php echo htmlspecialchars($delivery['city']); ?>, 
                                            <?php echo htmlspecialchars($delivery['postal_code']); ?>
                                        </td>
                                        <td>
                                            <span class="badge <?php echo $deliveryController->getStatusBadgeClass($delivery['delivery_status']); ?>">
                                                <?php echo $deliveryController->formatStatus($delivery['delivery_status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo $deliveryController->formatDate($delivery['order_date']); ?></td>
                                        <td>
                                            <a href="<?php echo url('/admin/delivery-details.php?id=' . $delivery['id']); ?>" class="btn btn-sm btn-outline-primary">
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
