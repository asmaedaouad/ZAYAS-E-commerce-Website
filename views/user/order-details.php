<?php
// Include configuration
require_once '../../config/config.php';
require_once '../../config/Database.php';
require_once '../../controllers/OrderController.php';

// Set page title
$pageTitle = 'Order Details';
$customCss = 'order-details.css';

// Redirect if not logged in
if (!isLoggedIn()) {
    redirect('/views/auth/login.php');
}

// Get order ID
$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($orderId <= 0) {
    redirect('/views/user/account.php');
}

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Create order controller
$orderController = new OrderController($db);

// Get order details
$data = $orderController->getOrderDetails($orderId);
$order = $data['order'];

// Include header
include_once '../../includes/header.php';
?>

<!-- Order Details Section -->
<section class="order-details-section section-padding">
    <div class="container">
        <div class="order-details-header">
            <h1 class="page-title">Order #<?php echo $order['id']; ?></h1>
            <p class="order-date">Placed on <?php echo date('M d, Y', strtotime($order['created_at'])); ?></p>
        </div>

        <div class="order-details-content">
            <div class="row">
                <div class="col-lg-8">
                    <div class="order-items">
                        <h2 class="section-title">Order Items</h2>

                        <div class="items-list">
                            <?php foreach ($order['items'] as $item): ?>
                            <div class="order-item">
                                <div class="item-image">
                                    <img src="<?php echo url('/public/images/' . $item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                </div>

                                <div class="item-details">
                                    <h3 class="item-name"><?php echo htmlspecialchars($item['name']); ?></h3>
                                    <p class="item-price"><?php echo number_format($item['price'], 2); ?>DH x <?php echo $item['quantity']; ?></p>
                                </div>

                                <div class="item-total">
                                    <?php echo number_format($item['price'] * $item['quantity'], 2); ?>DH
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="shipping-info">
                        <h2 class="section-title">Shipping Information</h2>

                        <div class="shipping-details">
                            <p class="shipping-address">
                                <?php echo htmlspecialchars($order['delivery']['address']); ?><br>
                                <?php echo htmlspecialchars($order['delivery']['city']); ?>, <?php echo htmlspecialchars($order['delivery']['postal_code']); ?><br>
                                Phone: <?php echo htmlspecialchars($order['delivery']['phone']); ?>
                            </p>

                            <?php if (!empty($order['delivery']['delivery_notes'])): ?>
                            <p class="shipping-notes">
                                <strong>Notes:</strong> <?php echo htmlspecialchars($order['delivery']['delivery_notes']); ?>
                            </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="order-summary">
                        <h2 class="summary-title">Order Summary</h2>

                        <div class="summary-item">
                            <span class="summary-label">Subtotal</span>
                            <span class="summary-value"><?php echo number_format($order['total_amount'], 2); ?>DH</span>
                        </div>

                        <div class="summary-item">
                            <span class="summary-label">Shipping</span>
                            <span class="summary-value">Free</span>
                        </div>

                        <div class="summary-total">
                            <span class="summary-label">Total</span>
                            <span class="summary-value"><?php echo number_format($order['total_amount'], 2); ?>DH</span>
                        </div>

                        <div class="delivery-status">
                            <span class="status-label">Status:</span>
                            <span class="status-value <?php echo strtolower($order['delivery']['delivery_status']); ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $order['delivery']['delivery_status'])); ?>
                            </span>
                        </div>

                        <?php if (!empty($order['delivery']['delivery_date'])): ?>
                        <div class="delivery-date">
                            <span class="date-label">Estimated Delivery:</span>
                            <span class="date-value">
                                <?php echo date('M d, Y', strtotime($order['delivery']['delivery_date'])); ?>
                            </span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="order-summary-actions">
                        <div class="order-summary-header">
                            <h2 class="summary-title">Order Actions</h2>
                            <?php if (in_array(strtolower($order['delivery']['delivery_status']), ['pending', 'assigned'])): ?>
                            <form action="<?php echo url('/controllers/order/cancel.php'); ?>" method="post" class="cancel-order-form" onsubmit="return confirm('Are you sure you want to cancel this order?');">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <button type="submit" class="btn-cancel-x" title="Cancel Order">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>
                        <div class="order-actions">
                            <?php if (strtolower($order['delivery']['delivery_status']) === 'delivered'): ?>
                            <form action="<?php echo url('/controllers/order/return.php'); ?>" method="post" onsubmit="return confirm('Are you sure you want to return this order?');">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <button type="submit" class="btn-primary">Return Order</button>
                            </form>
                            <?php endif; ?>
                            <a href="<?php echo url('/views/user/account.php#orders'); ?>" class="btn-secondary">Back to Orders</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Include footer
include_once '../../includes/footer.php';
?>

