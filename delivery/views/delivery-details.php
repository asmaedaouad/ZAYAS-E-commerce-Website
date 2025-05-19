<?php
// Include configuration
require_once '../../config/config.php';
require_once '../../config/Database.php';
require_once '../controllers/DeliveryController.php';

// Check if user is logged in and is delivery personnel
if (!isLoggedIn() || !isDelivery()) {
    redirect('/views/auth/login.php');
}

// Get delivery ID
$deliveryId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($deliveryId <= 0) {
    redirect('/delivery/dashboard.php');
}

// Set page title
$pageTitle = 'Delivery Details';
$customCss = 'style.css';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Create delivery controller
$deliveryController = new DeliveryController($db);

// Get delivery details
$data = $deliveryController->getDeliveryById($deliveryId);

if (isset($data['error'])) {
    redirect('/delivery/dashboard.php');
}

$delivery = $data['delivery'];
$orderItems = $data['order_items'];

// No status update processing needed

// Include header
include_once '../includes/header.php';
?>

<!-- Back Button -->
<div class="row mb-4">
    <div class="col-md-12">
        <a href="<?php echo url('/delivery/dashboard.php'); ?>" class="btn btn-outline-brown">
            <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
        </a>
    </div>
</div>

<!-- Delivery Details -->
<div class="row">
    <div class="col-md-6">
        <div class="card delivery-details-card h-100">
            <div class="card-header d-flex align-items-center">
                <div class="status-icon me-3" style="background-color: var(--<?php echo $delivery['delivery_status']; ?>-color); width: 36px; height: 36px;">
                    <?php if ($delivery['delivery_status'] === 'pending'): ?>
                        <i class="fas fa-clock"></i>
                    <?php elseif ($delivery['delivery_status'] === 'assigned'): ?>
                        <i class="fas fa-clipboard-list"></i>
                    <?php elseif ($delivery['delivery_status'] === 'in_transit'): ?>
                        <i class="fas fa-truck"></i>
                    <?php elseif ($delivery['delivery_status'] === 'delivered'): ?>
                        <i class="fas fa-check-circle"></i>
                    <?php elseif ($delivery['delivery_status'] === 'returned'): ?>
                        <i class="fas fa-undo"></i>
                    <?php endif; ?>
                </div>
                <div>
                    <h5 class="mb-0">Delivery Information</h5>
                    <span class="badge <?php echo $deliveryController->getStatusBadgeClass($delivery['delivery_status']); ?>">
                        <?php echo $deliveryController->formatStatus($delivery['delivery_status']); ?>
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="delivery-info">
                    <h5>Order Details</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Order ID:</strong> #<?php echo $delivery['order_id']; ?></p>
                            <p><strong>Delivery ID:</strong> #<?php echo $delivery['id']; ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Order Date:</strong> <?php echo $deliveryController->formatDate($delivery['order_date']); ?></p>
                            <p><strong>Order Time:</strong> <?php echo date('h:i A', strtotime($delivery['order_date'])); ?></p>
                        </div>
                    </div>
                </div>

                <div class="delivery-info">
                    <h5>Customer Information</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Name:</strong> <?php echo htmlspecialchars($delivery['first_name'] . ' ' . $delivery['last_name']); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($delivery['email']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Phone:</strong> <?php echo htmlspecialchars($delivery['phone']); ?></p>
                            <?php if (!empty($delivery['user_phone'])): ?>
                                <p><strong>Alt. Phone:</strong> <?php echo htmlspecialchars($delivery['user_phone']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="delivery-info">
                    <h5>Shipping Address</h5>
                    <p><i class="fas fa-map-marker-alt me-2 text-danger"></i> <strong><?php echo htmlspecialchars($delivery['address']); ?></strong></p>
                    <p class="ms-4"><?php echo htmlspecialchars($delivery['city']); ?>, <?php echo htmlspecialchars($delivery['postal_code']); ?></p>
                </div>

                <?php if (!empty($delivery['delivery_notes'])): ?>
                <div class="delivery-info">
                    <h5>Delivery Notes</h5>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <?php echo htmlspecialchars($delivery['delivery_notes']); ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center">
                <div class="status-icon me-3" style="background-color: var(--<?php echo $delivery['delivery_status']; ?>-color); width: 36px; height: 36px;">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <div>
                    <h5 class="mb-0">Order Summary</h5>
                    <span class="badge <?php echo $deliveryController->getStatusBadgeClass($delivery['delivery_status']); ?>"><?php echo $deliveryController->formatCurrency($delivery['total_amount']); ?></span>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover order-items-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orderItems as $item): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="<?php echo url('/public/images/' . $item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" class="me-3">
                                            <span><?php echo htmlspecialchars($item['product_name']); ?></span>
                                        </div>
                                    </td>
                                    <td class="text-center"><?php echo $item['quantity']; ?></td>
                                    <td class="text-end"><?php echo $deliveryController->formatCurrency($item['price']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer total-summary">
                <div class="d-flex justify-content-between">
                    <h5 class="mb-0">Total:</h5>
                    <h5 class="mb-0"><?php echo $deliveryController->formatCurrency($delivery['total_amount']); ?></h5>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include_once '../includes/footer.php';
?>

