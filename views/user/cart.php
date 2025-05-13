<?php
// Include configuration
require_once '../../config/config.php';
require_once '../../config/Database.php';
require_once '../../controllers/CartController.php';

// Set page title
$pageTitle = 'Shopping Cart';
$customCss = 'cart.css';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Create cart controller
$cartController = new CartController($db);

// Get cart data
$cart = $cartController->viewCart();
$items = $cart['items'];
$totalPrice = $cart['total_price'];
$itemCount = $cart['item_count'];

// Include header
include_once '../../includes/header.php';
?>

<!-- Cart Section -->
<section class="cart-section section-padding">
    <div class="container">
        <h1 class="page-title">Shopping Cart</h1>
        
        <?php if (empty($items)): ?>
        <div class="empty-cart">
            <p>Your cart is empty.</p>
            <a href="<?php echo url('/views/home/shop.php'); ?>" class="btn-primary">Continue Shopping</a>
        </div>
        <?php else: ?>
        <div class="cart-content">
            <div class="row">
                <div class="col-lg-8">
                    <div class="cart-items">
                        <table class="cart-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $item): ?>
                                <tr>
                                    <td class="product-info">
                                        <div class="product-image">
                                            <img src="<?php echo url('/public/images/' . $item['product']['image_path']); ?>" alt="<?php echo htmlspecialchars($item['product']['name']); ?>">
                                        </div>
                                        <div class="product-details">
                                            <h3 class="product-title">
                                                <a href="<?php echo url('/views/home/product.php?id=' . $item['product']['id']); ?>">
                                                    <?php echo htmlspecialchars($item['product']['name']); ?>
                                                </a>
                                            </h3>
                                            <p class="product-type"><?php echo ucfirst(htmlspecialchars($item['product']['type'])); ?></p>
                                        </div>
                                    </td>
                                    <td class="product-price">
                                        $<?php echo number_format($item['product']['price'], 2); ?>
                                    </td>
                                    <td class="product-quantity">
                                        <form action="<?php echo url('/controllers/cart/update.php'); ?>" method="post" class="quantity-form">
                                            <input type="hidden" name="product_id" value="<?php echo $item['product']['id']; ?>">
                                            <div class="quantity-input">
                                                <button type="button" class="quantity-btn minus">-</button>
                                                <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['product']['quantity']; ?>">
                                                <button type="button" class="quantity-btn plus">+</button>
                                            </div>
                                            <button type="submit" class="update-btn">Update</button>
                                        </form>
                                    </td>
                                    <td class="product-total">
                                        $<?php echo number_format($item['total'], 2); ?>
                                    </td>
                                    <td class="product-remove">
                                        <form action="<?php echo url('/controllers/cart/remove.php'); ?>" method="post">
                                            <input type="hidden" name="product_id" value="<?php echo $item['product']['id']; ?>">
                                            <button type="submit" class="remove-btn">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="cart-summary">
                        <h2 class="summary-title">Cart Summary</h2>
                        
                        <div class="summary-item">
                            <span class="summary-label">Subtotal</span>
                            <span class="summary-value">$<?php echo number_format($totalPrice, 2); ?></span>
                        </div>
                        
                        <div class="summary-item">
                            <span class="summary-label">Shipping</span>
                            <span class="summary-value">Free</span>
                        </div>
                        
                        <div class="summary-total">
                            <span class="summary-label">Total</span>
                            <span class="summary-value">$<?php echo number_format($totalPrice, 2); ?></span>
                        </div>
                        
                        <div class="summary-actions">
                            <a href="<?php echo url('/views/user/checkout.php'); ?>" class="btn-primary">Proceed to Checkout</a>
                            <a href="<?php echo url('/views/home/shop.php'); ?>" class="btn-secondary">Continue Shopping</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Simple JavaScript for quantity selector -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const minusBtns = document.querySelectorAll('.quantity-btn.minus');
    const plusBtns = document.querySelectorAll('.quantity-btn.plus');
    
    minusBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            const input = this.parentNode.querySelector('input');
            let currentValue = parseInt(input.value);
            if (currentValue > 1) {
                input.value = currentValue - 1;
            }
        });
    });
    
    plusBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            const input = this.parentNode.querySelector('input');
            let currentValue = parseInt(input.value);
            let maxValue = parseInt(input.getAttribute('max'));
            if (currentValue < maxValue) {
                input.value = currentValue + 1;
            }
        });
    });
});
</script>

<?php
// Include footer
include_once '../../includes/footer.php';
?>
