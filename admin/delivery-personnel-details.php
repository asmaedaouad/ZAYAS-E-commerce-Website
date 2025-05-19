<?php
// Include configuration
require_once '../config/config.php';
require_once '../config/Database.php';
require_once './controllers/AdminDeliveryController.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('/views/auth/login.php');
}

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect('/admin/delivery-personnel.php');
}

// Set page title
$pageTitle = 'Delivery Personnel Details';
$customCss = 'delivery.css';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Create delivery controller
$deliveryController = new AdminDeliveryController($db);

// Get personnel ID
$personnelId = (int)$_GET['id'];

// Get personnel details
$personnel = $deliveryController->getDeliveryPersonnelById($personnelId);

// If personnel not found, redirect
if (!$personnel) {
    redirect('/admin/delivery-personnel.php');
}

// Get assigned deliveries
$assignedDeliveries = $deliveryController->getDeliveriesByPersonnelId($personnelId);

// Get delivery statistics
$deliveryStats = $deliveryController->getDeliveryStatsByPersonnelId($personnelId);

// Include header
include_once './includes/header.php';
?>

<!-- Delivery Personnel Details Content -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-truck me-2"></i> Delivery Personnel Details</span>
                <div>
                    <a href="<?php echo url('/admin/delivery-personnel-form.php?id=' . $personnel['id']); ?>" class="btn btn-sm btn-outline-primary me-2">
                        <i class="fas fa-edit me-1"></i> Edit
                    </a>
                    <a href="<?php echo url('/admin/delivery-personnel.php'); ?>" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row equal-height-row">
    <!-- Personnel Information -->
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="fas fa-info-circle me-2"></i> Personnel Information
            </div>
            <div class="card-body d-flex flex-column">
                <div class="mb-3">
                    <h5 class="mb-0"><?php echo htmlspecialchars($personnel['first_name'] . ' ' . $personnel['last_name']); ?></h5>
                    <p class="text-muted mb-0">ID: <?php echo $personnel['id']; ?></p>
                </div>
                <hr>
                <div class="mb-2">
                    <strong><i class="fas fa-envelope me-2"></i> Email:</strong>
                    <p class="mb-0"><?php echo htmlspecialchars($personnel['email']); ?></p>
                </div>
                <div class="mb-2">
                    <strong><i class="fas fa-phone me-2"></i> Phone:</strong>
                    <p class="mb-0"><?php echo !empty($personnel['phone']) ? htmlspecialchars($personnel['phone']) : 'N/A'; ?></p>
                </div>
                <?php if (!empty($personnel['address']) || !empty($personnel['city']) || !empty($personnel['postal_code'])): ?>
                <div class="mb-2">
                    <strong><i class="fas fa-map-marker-alt me-2"></i> Address:</strong>
                    <p class="mb-0">
                        <?php
                        $addressParts = [];
                        if (!empty($personnel['address'])) $addressParts[] = htmlspecialchars($personnel['address']);
                        if (!empty($personnel['city'])) $addressParts[] = htmlspecialchars($personnel['city']);
                        if (!empty($personnel['postal_code'])) $addressParts[] = htmlspecialchars($personnel['postal_code']);
                        echo !empty($addressParts) ? implode(', ', $addressParts) : 'N/A';
                        ?>
                    </p>
                </div>
                <?php endif; ?>
                <div class="mb-2">
                    <strong><i class="fas fa-calendar-alt me-2"></i> Created:</strong>
                    <p class="mb-0"><?php echo isset($personnel['created_at']) ? $deliveryController->formatDate($personnel['created_at']) : 'N/A'; ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Delivery Statistics -->
    <div class="col-md-8 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="fas fa-chart-bar me-2"></i> Delivery Statistics
            </div>
            <div class="card-body d-flex flex-column">
                <div class="row stats-container">
                    <div class="col-md-3 mb-3">
                        <div class="stat-card p-3 text-center">
                            <h3 class="mb-1"><?php echo $deliveryStats['total'] ?? 0; ?></h3>
                            <p class="mb-0">Total Deliveries</p>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="stat-card p-3 text-center" style="border-left: 4px solid #4285F4;">
                            <h3 class="mb-1" style="color: #4285F4;"><?php echo $deliveryStats['in_transit'] ?? 0; ?></h3>
                            <p class="mb-0">In Transit</p>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="stat-card p-3 text-center" style="border-left: 4px solid #34A853;">
                            <h3 class="mb-1" style="color: #34A853;"><?php echo $deliveryStats['delivered'] ?? 0; ?></h3>
                            <p class="mb-0">Delivered</p>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="stat-card p-3 text-center" style="border-left: 4px solid #EA4335;">
                            <h3 class="mb-1" style="color: #EA4335;"><?php echo $deliveryStats['returned'] ?? 0; ?></h3>
                            <p class="mb-0">Returned</p>
                        </div>
                    </div>
                </div>

                <?php if (isset($deliveryStats['completion_rate'])): ?>
                <div class="mt-auto pt-4">
                    <h6 class="mb-2">Delivery Completion Rate</h6>
                    <div class="progress" style="height: 20px; border-radius: 10px; background-color: #f0e6d9;">
                        <div class="progress-bar bg-success" role="progressbar"
                             style="width: <?php echo $deliveryStats['completion_rate']; ?>%; border-radius: 10px; font-weight: 600;"
                             aria-valuenow="<?php echo $deliveryStats['completion_rate']; ?>"
                             aria-valuemin="0"
                             aria-valuemax="100">
                            <?php echo $deliveryStats['completion_rate']; ?>%
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Assigned Deliveries -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-truck me-2"></i> Assigned Deliveries
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
                            <?php if (empty($assignedDeliveries)): ?>
                                <tr>
                                    <td colspan="7" class="text-center">No deliveries assigned to this personnel</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($assignedDeliveries as $delivery): ?>
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
                                                <i class="fas fa-truck"></i> View Delivery
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

