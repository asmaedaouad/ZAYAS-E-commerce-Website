<?php
// Include configuration
require_once '../config/config.php';
require_once '../config/Database.php';
require_once './controllers/AdminOrderController.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('/views/auth/login.php');
}

// Check if order ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect('/admin/orders.php');
}

$orderId = $_GET['id'];

// Set page title
$pageTitle = 'Order Details';
$customCss = 'order-details.css';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Create order controller
$orderController = new AdminOrderController($db);

// Get order details
$order = $orderController->getOrderById($orderId);

// If order not found, redirect to orders page
if (!$order) {
    redirect('/admin/orders.php');
}

// Get order items
$orderItems = $orderController->getOrderItems($orderId);

// Get delivery information
$deliveryInfo = $orderController->getDeliveryInfo($orderId);

// Status updates are now handled by delivery personnel only
// No status update message variable needed

// Include header
include_once './includes/header.php';
?>

<!-- Order Details Content -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-info-circle me-2"></i> Order #<?php echo $order['id']; ?> Details</span>
                <a href="<?php echo url('/admin/orders.php'); ?>" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Orders
                </a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Order Information</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th>Order ID</th>
                                <td>#<?php echo $order['id']; ?></td>
                            </tr>
                            <tr>
                                <th>Date</th>
                                <td><?php echo $orderController->formatDate($order['created_at']); ?></td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    <span class="badge <?php echo $orderController->getStatusBadgeClass($order['status']); ?>">
                                        <?php echo $orderController->formatStatus($order['status']); ?>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Total Amount</th>
                                <td><?php echo $orderController->formatCurrency($order['total_amount']); ?></td>
                            </tr>
                        </table>
                    </div>

                    <div class="col-md-6">
                        <h5>Customer Information</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th>Name</th>
                                <td><?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td><?php echo htmlspecialchars($order['email']); ?></td>
                            </tr>
                            <tr>
                                <th>Phone</th>
                                <td><?php echo htmlspecialchars($order['phone'] ?? 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <th>Address</th>
                                <td>
                                    <?php if (!empty($order['address'])): ?>
                                        <?php echo htmlspecialchars($order['address']); ?><br>
                                        <?php echo htmlspecialchars($order['city'] ?? ''); ?><br>
                                        <?php echo htmlspecialchars($order['postal_code'] ?? ''); ?>
                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <?php if ($deliveryInfo): ?>
                <div class="row mt-4">
                    <div class="col-md-12">
                        <h5>Delivery Information</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th>Delivery Address</th>
                                <td>
                                    <?php echo htmlspecialchars($deliveryInfo['address']); ?><br>
                                    <?php echo htmlspecialchars($deliveryInfo['city']); ?><br>
                                    <?php echo htmlspecialchars($deliveryInfo['postal_code']); ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Phone</th>
                                <td><?php echo htmlspecialchars($deliveryInfo['phone']); ?></td>
                            </tr>
                            <tr>
                                <th>Delivery Personnel</th>
                                <td>
                                    <?php if (!empty($deliveryInfo['personnel_id'])): ?>
                                        <?php echo htmlspecialchars($deliveryInfo['personnel_first_name'] . ' ' . $deliveryInfo['personnel_last_name']); ?>
                                        <?php if (!empty($deliveryInfo['personnel_phone'])): ?>
                                            <br><small class="text-muted">Phone: <?php echo htmlspecialchars($deliveryInfo['personnel_phone']); ?></small>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted">Not assigned yet</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php if (!empty($deliveryInfo['delivery_notes'])): ?>
                            <tr>
                                <th>Notes</th>
                                <td><?php echo htmlspecialchars($deliveryInfo['delivery_notes']); ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if (!empty($deliveryInfo['delivery_date'])): ?>
                            <tr>
                                <th>Delivery Date</th>
                                <td><?php echo $orderController->formatDate($deliveryInfo['delivery_date']); ?></td>
                            </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
                <?php endif; ?>

                <div class="row mt-4">
                    <div class="col-md-12">
                        <h5>Order Items</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Type</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orderItems as $item): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="<?php echo url('/public/images/' . $item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="product-thumbnail me-2">
                                                    <span><?php echo htmlspecialchars($item['name']); ?></span>
                                                </div>
                                            </td>
                                            <td><?php echo ucfirst($item['type']); ?></td>
                                            <td><?php echo $orderController->formatCurrency($item['price']); ?></td>
                                            <td><?php echo $item['quantity']; ?></td>
                                            <td><?php echo $orderController->formatCurrency($item['price'] * $item['quantity']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="4" class="text-end">Total:</th>
                                        <th><?php echo $orderController->formatCurrency($order['total_amount']); ?></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include_once './includes/footer.php';
?>

