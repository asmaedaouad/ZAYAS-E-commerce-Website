<?php
// Include configuration
require_once '../config/config.php';
require_once '../config/Database.php';
require_once './controllers/AdminDeliveryController.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('/views/auth/login.php');
}

// Set page title
$pageTitle = 'Delivery Management';
$customCss = 'delivery.css';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Create delivery controller
$deliveryController = new AdminDeliveryController($db);

// Get all deliveries
$deliveries = $deliveryController->getDeliveries();

// Get delivery status counts
$statusCounts = $deliveryController->getDeliveryStatusCounts();

// Include header
include_once './includes/header.php';
?>

<!-- Delivery Content -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-truck me-2"></i> Delivery Management</span>
                <a href="<?php echo url('/admin/delivery-personnel.php'); ?>" class="btn btn-sm btn-primary">
                    <i class="fas fa-people-carry me-1"></i> Manage Delivery Personnel
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="status-cards">
            <div class="status-card active">
                <div class="status-card-body">
                    <h3><?php echo array_sum($statusCounts); ?></h3>
                    <p>All Deliveries</p>
                </div>
            </div>

            <div class="status-card">
                <div class="status-card-body">
                    <h3><?php echo isset($statusCounts['pending']) ? $statusCounts['pending'] : 0; ?></h3>
                    <p>Pending</p>
                </div>
            </div>

            <div class="status-card">
                <div class="status-card-body">
                    <h3><?php echo isset($statusCounts['assigned']) ? $statusCounts['assigned'] : 0; ?></h3>
                    <p>Assigned</p>
                </div>
            </div>

            <div class="status-card">
                <div class="status-card-body">
                    <h3><?php echo isset($statusCounts['in_transit']) ? $statusCounts['in_transit'] : 0; ?></h3>
                    <p>In Transit</p>
                </div>
            </div>

            <div class="status-card">
                <div class="status-card-body">
                    <h3><?php echo isset($statusCounts['delivered']) ? $statusCounts['delivered'] : 0; ?></h3>
                    <p>Delivered</p>
                </div>
            </div>

            <div class="status-card">
                <div class="status-card-body">
                    <h3><?php echo isset($statusCounts['cancelled']) ? $statusCounts['cancelled'] : 0; ?></h3>
                    <p>Cancelled</p>
                </div>
            </div>

            <div class="status-card">
                <div class="status-card-body">
                    <h3><?php echo isset($statusCounts['returned']) ? $statusCounts['returned'] : 0; ?></h3>
                    <p>Returned</p>
                </div>
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

