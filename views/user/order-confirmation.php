<?php
// Include configuration
require_once '../../config/config.php';
require_once '../../config/Database.php';
require_once '../../controllers/OrderController.php';

// Set page title
$pageTitle = 'Order Confirmation';
$customCss = 'order-confirmation.css';

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

<!-- Order Confirmation Section -->
<section class="confirmation-section section-padding">
    <div class="container">
        <div class="confirmation-content">
            <div class="confirmation-header">
                <i class="fas fa-check-circle confirmation-icon"></i>
                <h1 class="confirmation-title">Thank You for Your Order!</h1>
                <p class="confirmation-message">Your order has been placed successfully.</p>
            </div>

            <div class="order-details">
                <h2 class="details-title">Order Details</h2>

                <div class="details-info">
                    <div class="info-item">
                        <span class="info-label">Order Number:</span>
                        <span class="info-value">#<?php echo $order['id']; ?></span>
                    </div>

                    <div class="info-item">
                        <span class="info-label">Date:</span>
                        <span class="info-value"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></span>
                    </div>

                    <div class="info-item">
                        <span class="info-label">Total:</span>
                        <span class="info-value">$<?php echo number_format($order['total_amount'], 2); ?></span>
                    </div>

                    <div class="info-item">
                        <span class="info-label">Payment Method:</span>
                        <span class="info-value">Cash on Delivery</span>
                    </div>
                </div>
            </div>

            <div class="order-items">
                <h2 class="items-title">Order Items</h2>

                <div class="items-list">
                    <?php foreach ($order['items'] as $item): ?>
                    <div class="item">
                        <div class="item-image">
                            <img src="<?php echo url('/public/images/' . $item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                        </div>

                        <div class="item-details">
                            <h3 class="item-name"><?php echo htmlspecialchars($item['name']); ?></h3>
                            <p class="item-price">$<?php echo number_format($item['price'], 2); ?> x <?php echo $item['quantity']; ?></p>
                        </div>

                        <div class="item-total">
                            $<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="shipping-info">
                <h2 class="shipping-title">Shipping Information</h2>

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

            <div class="confirmation-actions">
                <a href="<?php echo url('/views/user/account.php#orders'); ?>" class="btn-secondary">View Orders</a>
                <a href="<?php echo url('/views/home/shop.php'); ?>" class="btn-primary">Continue Shopping</a>
            </div>
        </div>
    </div>
</section>

<?php
// Include footer
include_once '../../includes/footer.php';
?>

