<?php
// Include configuration
require_once '../config/config.php';
require_once '../config/Database.php';
require_once './controllers/DeliveryController.php';

// Check if user is logged in and is delivery personnel
if (!isLoggedIn() || !isDelivery()) {
    redirect('/views/auth/login.php');
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

// Get total pending orders count
$totalPendingCount = $deliveryController->getTotalPendingOrdersCount();

// Include header
include_once './includes/header.php';
?>

<!-- Status Messages -->
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

<!-- Status Cards -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="status-cards">
            <div class="status-card pending" title="Total pending orders in the system">
                <div class="status-card-body">
                    <div class="status-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3><?php echo $totalPendingCount; ?></h3>
                    <p>All Pending</p>
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

            <div class="status-card cancelled">
                <div class="status-card-body">
                    <div class="status-icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <h3><?php echo isset($statusCounts['cancelled']) ? $statusCounts['cancelled'] : 0; ?></h3>
                    <p>Cancelled</p>
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
                    <table class="table">
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
                                    <td colspan="6" class="text-center py-5">
                                        <i class="fas fa-inbox fa-3x mb-3 text-muted"></i>
                                        <p class="text-muted mt-2">No deliveries assigned to you yet</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($deliveries as $delivery): ?>
                                    <tr>
                                        <td>
                                            <strong class="text-primary-dark">#<?php echo $delivery['order_id']; ?></strong>
                                            <div class="small text-muted">ID: <?php echo $delivery['id']; ?></div>
                                        </td>
                                        <td>
                                            <div class="fw-medium"><?php echo htmlspecialchars($delivery['first_name'] . ' ' . $delivery['last_name']); ?></div>
                                            <?php if (!empty($delivery['phone'])): ?>
                                                <div class="small text-muted"><i class="fas fa-phone-alt me-1"></i><?php echo htmlspecialchars($delivery['phone']); ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <i class="fas fa-map-marker-alt me-1 text-muted"></i>
                                            <?php echo htmlspecialchars($delivery['city']); ?>,
                                            <?php echo htmlspecialchars($delivery['postal_code']); ?>
                                        </td>
                                        <td>
                                            <span class="badge <?php echo $deliveryController->getStatusBadgeClass($delivery['delivery_status']); ?>">
                                                <?php if ($delivery['delivery_status'] === 'pending'): ?>
                                                    <i class="fas fa-clock"></i>
                                                <?php elseif ($delivery['delivery_status'] === 'assigned'): ?>
                                                    <i class="fas fa-clipboard-list"></i>
                                                <?php elseif ($delivery['delivery_status'] === 'in_transit'): ?>
                                                    <i class="fas fa-truck"></i>
                                                <?php elseif ($delivery['delivery_status'] === 'delivered'): ?>
                                                    <i class="fas fa-check-circle"></i>
                                                <?php elseif ($delivery['delivery_status'] === 'cancelled'): ?>
                                                    <i class="fas fa-times-circle"></i>
                                                <?php elseif ($delivery['delivery_status'] === 'returned'): ?>
                                                    <i class="fas fa-undo"></i>
                                                <?php endif; ?>
                                                <?php echo $deliveryController->formatStatus($delivery['delivery_status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div><i class="far fa-calendar-alt me-1 text-muted"></i><?php echo $deliveryController->formatDate($delivery['order_date']); ?></div>
                                            <div class="small text-muted"><i class="far fa-clock me-1"></i><?php echo date('h:i A', strtotime($delivery['order_date'])); ?></div>
                                        </td>
                                        <td class="d-flex gap-2">
                                            <a href="<?php echo url('/delivery/views/delivery-details.php?id=' . $delivery['id']); ?>" class="btn btn-sm btn-outline-brown">
                                                <i class="fas fa-eye me-1"></i> View
                                            </a>

                                            <?php if ($delivery['delivery_status'] === 'assigned'): ?>
                                                <form action="<?php echo url('/delivery/update-status.php'); ?>" method="POST" onsubmit="return confirm('Are you sure you want to change the status to In Transit? This means you are accepting this delivery and will start the delivery process.');">
                                                    <input type="hidden" name="delivery_id" value="<?php echo $delivery['id']; ?>">
                                                    <input type="hidden" name="status" value="in_transit">
                                                    <button type="submit" class="btn btn-sm btn-in-transit">
                                                        <i class="fas fa-truck me-1"></i> Accept & Start
                                                    </button>
                                                </form>
                                            <?php elseif ($delivery['delivery_status'] === 'in_transit'): ?>
                                                <form action="<?php echo url('/delivery/update-status.php'); ?>" method="POST" onsubmit="return confirm('Are you sure you want to change the status to Delivered? This means you have successfully delivered the order to the customer.');">
                                                    <input type="hidden" name="delivery_id" value="<?php echo $delivery['id']; ?>">
                                                    <input type="hidden" name="status" value="delivered">
                                                    <button type="submit" class="btn btn-sm btn-delivered">
                                                        <i class="fas fa-check-circle me-1"></i> Mark Delivered
                                                    </button>
                                                </form>
                                            <?php elseif ($delivery['delivery_status'] === 'delivered'): ?>
                                                <form action="<?php echo url('/delivery/update-status.php'); ?>" method="POST" onsubmit="return confirm('Are you sure you want to change the status to Returned? Only do this if the customer returns the order after delivery.');">
                                                    <input type="hidden" name="delivery_id" value="<?php echo $delivery['id']; ?>">
                                                    <input type="hidden" name="status" value="returned">
                                                    <button type="submit" class="btn btn-sm btn-returned">
                                                        <i class="fas fa-undo me-1"></i> Mark Returned
                                                    </button>
                                                </form>
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

