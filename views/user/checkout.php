<?php
// Include configuration
require_once '../../config/config.php';
require_once '../../config/Database.php';
require_once '../../controllers/OrderController.php';

// Set page title
$pageTitle = 'Checkout';
$customCss = 'checkout.css';

// Redirect if not logged in
if (!isLoggedIn()) {
    redirect('/views/auth/login.php');
}

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Create order controller
$orderController = new OrderController($db);

// Handle checkout
$data = $orderController->createOrder();
$errors = $data['errors'];
$address = $data['address'];
$city = $data['city'];
$postalCode = $data['postal_code'];
$phone = $data['phone'];
$notes = $data['notes'];
$cart = $data['cart'];

// Include header
include_once '../../includes/header.php';
?>

<!-- Checkout Section -->
<section class="checkout-section">
    <div class="container">
        <h1 class="page-title">Checkout</h1>

        <!-- Checkout Progress -->
        <div class="checkout-progress">
            <div class="progress-step completed">
                <div class="step-number">1</div>
                <div class="step-label">Shopping Cart</div>
            </div>
            <div class="progress-step active">
                <div class="step-number">2</div>
                <div class="step-label">Checkout</div>
            </div>
            <div class="progress-step">
                <div class="step-number">3</div>
                <div class="step-label">Order Complete</div>
            </div>
        </div>

        <?php if (empty($cart['items'])): ?>
        <div class="empty-cart">
            <p>Your cart is empty.</p>
            <a href="<?php echo url('/views/home/shop.php'); ?>" class="btn-primary">Continue Shopping</a>
        </div>
        <?php else: ?>

        <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <div class="checkout-content">
            <div class="row">
                <div class="col-lg-8">
                    <div class="checkout-form">
                        <h2 class="form-title">Shipping Information</h2>

                        <form action="<?php echo url('/views/user/checkout.php'); ?>" method="post">
                            <div class="form-group">
                                <label for="address">Address</label>
                                <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($address); ?>" required placeholder="Enter your street address">
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="city">City</label>
                                        <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($city); ?>" required placeholder="Enter your city">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="postal_code">Postal Code</label>
                                        <input type="text" id="postal_code" name="postal_code" value="<?php echo htmlspecialchars($postalCode); ?>" required placeholder="Enter your postal code">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>" required placeholder="Enter your phone number">
                            </div>

                            <div class="form-group">
                                <label for="notes">Order Notes (Optional)</label>
                                <textarea id="notes" name="notes" rows="4" placeholder="Special instructions for delivery or any other notes"><?php echo htmlspecialchars($notes); ?></textarea>
                            </div>

                            <h2 class="form-title mt-4">Payment Method</h2>

                            <div class="payment-methods">
                                <div class="payment-method">
                                    <input type="radio" id="payment_cod" name="payment_method" value="cod" checked>
                                    <label for="payment_cod">Cash on Delivery</label>
                                </div>
                            </div>

                            <button type="submit" class="btn-primary">Place Order</button>
                        </form>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="order-summary">
                        <h2 class="summary-title">Order Summary</h2>

                        <div class="summary-items">
                            <?php foreach ($cart['items'] as $item): ?>
                            <div class="summary-item">
                                <div class="item-info">
                                    <span class="item-name"><?php echo htmlspecialchars($item['product']['name']); ?></span>
                                    <span class="item-quantity">x<?php echo $item['quantity']; ?></span>
                                </div>
                                <span class="item-price"><?php echo number_format($item['total'], 2); ?>DH</span>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="summary-subtotal">
                            <span class="subtotal-label">Subtotal</span>
                            <span class="subtotal-value"><?php echo number_format($cart['total_price'], 2); ?>DH</span>
                        </div>

                        <div class="summary-shipping">
                            <span class="shipping-label">Shipping</span>
                            <span class="shipping-value">Free</span>
                        </div>

                        <div class="summary-total">
                            <span class="total-label">Total</span>
                            <span class="total-value"><?php echo number_format($cart['total_price'], 2); ?>DH</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Add JavaScript for better user experience -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Highlight the selected payment method
    const paymentMethods = document.querySelectorAll('.payment-method');

    paymentMethods.forEach(method => {
        method.addEventListener('click', function() {
            // Find the radio input within this payment method
            const radio = this.querySelector('input[type="radio"]');
            if (radio) {
                radio.checked = true;
            }

            // Remove active class from all payment methods
            paymentMethods.forEach(m => m.classList.remove('active'));

            // Add active class to the clicked payment method
            this.classList.add('active');
        });

        // Check if this payment method is already selected
        const radio = method.querySelector('input[type="radio"]');
        if (radio && radio.checked) {
            method.classList.add('active');
        }
    });

    // Form validation
    const checkoutForm = document.querySelector('.checkout-form form');

    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(event) {
            let isValid = true;

            // Get all required inputs
            const requiredInputs = this.querySelectorAll('input[required], textarea[required]');

            requiredInputs.forEach(input => {
                if (!input.value.trim()) {
                    isValid = false;
                    input.classList.add('is-invalid');
                } else {
                    input.classList.remove('is-invalid');
                }
            });

            if (!isValid) {
                event.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
    }
});
</script>

<?php
// Include footer
include_once '../../includes/footer.php';
?>

