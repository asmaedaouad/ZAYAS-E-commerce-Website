<?php
// Include configuration
require_once '../config/config.php';
require_once '../config/Database.php';
require_once './controllers/DeliveryController.php';

// Check if user is logged in and is delivery personnel
if (!isLoggedIn() || !isDelivery()) {
    redirect('/delivery/login.php');
}

// Set page title
$pageTitle = 'Delivery Dashboard';
$customCss = 'style.css';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Create delivery controller
$deliveryController = new DeliveryController($db);

// Get assigned deliveries
$deliveries = $deliveryController->getAssignedDeliveries();

// Get delivery status counts
$statusCounts = $deliveryController->getDeliveryStatusCounts();

// Include header
include_once './includes/header.php';
?>

<!-- Status Cards -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="status-cards">
            <div class="status-card pending">
                <div class="status-card-body">
                    <div class="status-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3><?php echo isset($statusCounts['pending']) ? $statusCounts['pending'] : 0; ?></h3>
                    <p>Pending</p>
                </div>
            </div>

            <div class="status-card assigned">
                <div class="status-card-body">
                    <div class="status-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <h3><?php echo isset($statusCounts['assigned']) ? $statusCounts['assigned'] : 0; ?></h3>
                    <p>Assigned</p>
                </div>
            </div>

            <div class="status-card in-transit">
                <div class="status-card-body">
                    <div class="status-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <h3><?php echo isset($statusCounts['in_transit']) ? $statusCounts['in_transit'] : 0; ?></h3>
                    <p>In Transit</p>
                </div>
            </div>

            <div class="status-card delivered">
                <div class="status-card-body">
                    <div class="status-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h3><?php echo isset($statusCounts['delivered']) ? $statusCounts['delivered'] : 0; ?></h3>
                    <p>Delivered</p>
                </div>
            </div>

            <div class="status-card returned">
                <div class="status-card-body">
                    <div class="status-icon">
                        <i class="fas fa-undo"></i>
                    </div>
                    <h3><?php echo isset($statusCounts['returned']) ? $statusCounts['returned'] : 0; ?></h3>
                    <p>Returned</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Deliveries Table -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-truck me-2"></i> My Assigned Deliveries
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Address</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($deliveries)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <i class="fas fa-inbox fa-2x mb-3 text-muted"></i>
                                        <p class="text-muted">No deliveries assigned to you yet</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($deliveries as $delivery): ?>
                                    <tr>
                                        <td>
                                            <strong>#<?php echo $delivery['order_id']; ?></strong>
                                            <div class="small text-muted">ID: <?php echo $delivery['id']; ?></div>
                                        </td>
                                        <td>
                                            <div><?php echo htmlspecialchars($delivery['first_name'] . ' ' . $delivery['last_name']); ?></div>
                                            <?php if (!empty($delivery['phone'])): ?>
                                                <div class="small text-muted"><?php echo htmlspecialchars($delivery['phone']); ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($delivery['city']); ?>,
                                            <?php echo htmlspecialchars($delivery['postal_code']); ?>
                                        </td>
                                        <td>
                                            <span class="badge <?php echo $deliveryController->getStatusBadgeClass($delivery['delivery_status']); ?>">
                                                <?php echo $deliveryController->formatStatus($delivery['delivery_status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div><?php echo $deliveryController->formatDate($delivery['order_date']); ?></div>
                                            <div class="small text-muted"><?php echo date('h:i A', strtotime($delivery['order_date'])); ?></div>
                                        </td>
                                        <td>
                                            <?php if ($delivery['delivery_status'] === 'assigned'): ?>
                                                <a href="<?php echo url('/delivery/views/delivery-details.php?id=' . $delivery['id']); ?>" class="btn btn-sm btn-success">
                                                    <i class="fas fa-check"></i> Accept
                                                </a>
                                            <?php else: ?>
                                                <a href="<?php echo url('/delivery/views/delivery-details.php?id=' . $delivery['id']); ?>" class="btn btn-sm btn-outline-brown">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            <?php endif; ?>
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
