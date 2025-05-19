<?php
// Include configuration
require_once '../../config/config.php';
require_once '../../config/Database.php';
require_once '../../controllers/UserController.php';

// Set page title
$pageTitle = 'My Account';
$customCss = 'account.css';

// Redirect if not logged in
if (!isLoggedIn()) {
    redirect('/views/auth/login.php');
}

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Create user controller
$userController = new UserController($db);

// Get account data
$data = $userController->account();
$user = $data['user'];
$orders = $data['orders'];
$wishlist = $data['wishlist'];
$cart = $data['cart']; // Make sure cart data is assigned to a variable

// Check if cart table exists and create it if it doesn't
$checkTableQuery = "SHOW TABLES LIKE 'cart'";
$stmt = $db->prepare($checkTableQuery);
$stmt->execute();
$tableExists = $stmt->rowCount() > 0;

if (!$tableExists) {
    // Create cart table
    $createTableQuery = "CREATE TABLE IF NOT EXISTS cart (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
        UNIQUE KEY (user_id, product_id)
    )";

    try {
        $db->exec($createTableQuery);
    } catch (PDOException $e) {
        // Handle error
    }
}

// Get cart data directly from database
$userId = $_SESSION['user_id'];
$cartQuery = "SELECT c.*, p.name, p.price, p.image_path, p.quantity as stock_quantity, p.type
             FROM cart c
             JOIN products p ON c.product_id = p.id
             WHERE c.user_id = :user_id";
$stmt = $db->prepare($cartQuery);
$stmt->bindParam(':user_id', $userId);
$stmt->execute();
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Format cart data for display
$formattedCart = [
    'items' => [],
    'total_price' => 0,
    'item_count' => count($cartItems)
];

foreach ($cartItems as $item) {
    $itemTotal = $item['price'] * $item['quantity'];
    $formattedCart['total_price'] += $itemTotal;

    $formattedCart['items'][] = [
        'product' => [
            'id' => $item['product_id'],
            'name' => $item['name'],
            'price' => $item['price'],
            'image_path' => $item['image_path'],
            'quantity' => $item['stock_quantity'],
            'type' => $item['type']
        ],
        'quantity' => $item['quantity'],
        'total' => $itemTotal
    ];
}

// Replace the cart data with our directly queried data
$cart = $formattedCart;

// Debug cart data
if (isset($_GET['debug'])) {
    echo '<pre>';
    print_r($cart);
    echo '</pre>';
}

// Include header
include_once '../../includes/header.php';
?>

<!-- Account Section -->
<section class="account-section section-padding">
    <div class="container">
        <h1 class="page-title">My Account</h1>

        <div class="row">
            <!-- Account Navigation -->
            <div class="col-lg-3">
                <div class="account-nav">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="#profile" data-bs-toggle="tab">Profile</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#orders" data-bs-toggle="tab">Orders</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#cart" data-bs-toggle="tab">Cart</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#wishlist" data-bs-toggle="tab">Wishlist</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#password" data-bs-toggle="tab">Change Password</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link logout-link" href="javascript:void(0);" onclick="confirmLogout('<?php echo url('/views/auth/logout.php'); ?>')">Logout</a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Account Content -->
            <div class="col-lg-9">
                <div class="tab-content">
                    <!-- Profile Tab -->
                    <div class="tab-pane fade show active" id="profile">
                        <div class="account-card">
                            <h2 class="card-title">Profile Information</h2>

                            <?php if (isset($_SESSION['profile_success'])): ?>
                            <div class="alert alert-success">
                                <?php echo $_SESSION['profile_success']; ?>
                                <?php unset($_SESSION['profile_success']); ?>
                            </div>
                            <?php endif; ?>

                            <form action="<?php echo url('/controllers/user/update-profile.php'); ?>" method="post" class="profile-form">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="first_name">First Name</label>
                                            <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="last_name">Last Name</label>
                                            <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                                </div>

                                <div class="form-group">
                                    <label for="address">Address</label>
                                    <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>">
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="city">City</label>
                                            <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($user['city'] ?? ''); ?>">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="postal_code">Postal Code</label>
                                            <input type="text" id="postal_code" name="postal_code" value="<?php echo htmlspecialchars($user['postal_code'] ?? ''); ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="phone">Phone</label>
                                    <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                                </div>

                                <button type="submit" class="btn-primary">Update Profile</button>
                            </form>
                        </div>
                    </div>

                    <!-- Orders Tab -->
                    <div class="tab-pane fade" id="orders">
                        <div class="account-card">
                            <h2 class="card-title">My Orders</h2>

                            <?php if (isset($_SESSION['order_success'])): ?>
                            <div class="alert alert-success">
                                <?php echo $_SESSION['order_success']; ?>
                                <?php unset($_SESSION['order_success']); ?>
                            </div>
                            <?php endif; ?>

                            <?php if (isset($_SESSION['order_error'])): ?>
                            <div class="alert alert-danger">
                                <?php echo $_SESSION['order_error']; ?>
                                <?php unset($_SESSION['order_error']); ?>
                            </div>
                            <?php endif; ?>

                            <?php if (empty($orders)): ?>
                            <div class="empty-orders">
                                <p>You have no orders yet.</p>
                                <a href="<?php echo url('/views/home/shop.php'); ?>" class="btn-primary continue-shopping">Start Shopping</a>
                            </div>
                            <?php else: ?>
                            <div class="orders-list">
                                <table class="orders-table">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Date</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td>#<?php echo $order['id']; ?></td>
                                            <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                            <td><?php echo number_format($order['total_amount'], 2); ?>DH</td>
                                            <td>
                                                <span class="order-status <?php echo strtolower($order['delivery']['delivery_status']); ?>">
                                                    <?php echo ucfirst(str_replace('_', ' ', $order['delivery']['delivery_status'])); ?>
                                                </span>
                                            </td>
                                            <td class="order-actions-cell">
                                                <div class="order-actions-container">
                                                    <a href="<?php echo url('/views/user/order-details.php?id=' . $order['id']); ?>" class="btn-view">
                                                        View Details
                                                    </a>
                                                    <?php if (in_array(strtolower($order['delivery']['delivery_status']), ['pending', 'assigned'])): ?>
                                                    <form action="<?php echo url('/controllers/order/cancel.php'); ?>" method="post" class="cancel-order-form" onsubmit="return confirm('Are you sure you want to cancel this order?');">
                                                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                        <button type="submit" class="btn-cancel-x" title="Cancel Order">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </form>
                                                    <?php elseif (strtolower($order['delivery']['delivery_status']) === 'delivered'): ?>
                                                    <form action="<?php echo url('/controllers/order/return.php'); ?>" method="post" class="return-order-form" onsubmit="return confirm('Are you sure you want to return this order?');">
                                                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                        <button type="submit" class="btn-return" title="Return Order">
                                                            <i class="fas fa-undo-alt"></i>
                                                        </button>
                                                    </form>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Cart Tab -->
                    <div class="tab-pane fade" id="cart">
                        <div class="account-card">
                            <div class="cart-header">
                                <h2 class="card-title">My Cart</h2>
                                <form action="<?php echo url('/controllers/cart/update.php'); ?>" method="post" id="cart-update-form">
                                    <button type="submit" class="update-all-btn" title="Update Cart">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </form>
                            </div>

                            <!-- Debug information -->
                            <?php if (isset($_GET['debug'])): ?>
                            <div style="background-color: #f8f9fa; padding: 10px; margin-bottom: 15px; border-radius: 4px;">
                                <h4>Debug Information</h4>
                                <pre><?php print_r($cart); ?></pre>
                            </div>
                            <?php endif; ?>

                            <?php if (empty($cart['items'])): ?>
                            <div class="empty-cart">
                                <p>Your cart is empty.</p>
                                <a href="<?php echo url('/views/home/shop.php'); ?>" class="btn-primary continue-shopping">Continue Shopping</a>
                            </div>
                            <?php else: ?>
                            <div class="cart-content">
                                <div class="cart-items">
                                    <form action="<?php echo url('/controllers/cart/update.php'); ?>" method="post" id="cart-items-form">
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
                                                <?php foreach ($cart['items'] as $item): ?>
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
                                                        <?php echo number_format($item['product']['price'], 2); ?>DH
                                                    </td>
                                                    <td class="product-quantity">
                                                        <input type="hidden" name="product_ids[]" value="<?php echo $item['product']['id']; ?>">
                                                        <div class="quantity-input">
                                                            <button type="button" class="quantity-btn minus">-</button>
                                                            <input type="number" name="quantities[]" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['product']['quantity']; ?>">
                                                            <button type="button" class="quantity-btn plus">+</button>
                                                        </div>
                                                    </td>
                                                    <td class="product-total">
                                                        <?php echo number_format($item['total'], 2); ?>DH
                                                    </td>
                                                    <td class="product-remove">
                                                        <?php
                                                        // Use GET method for removal instead of form submission
                                                        // This is more reliable for the first item in the cart
                                                        $removeUrl = url('/controllers/cart/remove.php?product_id=' . $item['product']['id']);
                                                        ?>
                                                        <a href="<?php echo $removeUrl; ?>" class="remove-btn" title="Remove from cart">
                                                            <i class="fas fa-times"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </form>
                                </div>

                                <div class="cart-summary">
                                    <h3 class="summary-title">Cart Summary</h3>

                                    <div class="summary-item">
                                        <span class="summary-label">Subtotal</span>
                                        <span class="summary-value"><?php echo number_format($cart['total_price'], 2); ?>DH</span>
                                    </div>

                                    <div class="summary-item">
                                        <span class="summary-label">Shipping</span>
                                        <span class="summary-value">Free</span>
                                    </div>

                                    <div class="summary-total">
                                        <span class="summary-label">Total</span>
                                        <span class="summary-value"><?php echo number_format($cart['total_price'], 2); ?>DH</span>
                                    </div>

                                    <div class="summary-actions">
                                        <a href="<?php echo url('/views/user/checkout.php'); ?>" class="btn-primary">Proceed to Checkout</a>
                                        <a href="<?php echo url('/views/home/shop.php'); ?>" class="btn-secondary">Continue Shopping</a>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Wishlist Tab -->
                    <div class="tab-pane fade" id="wishlist">
                        <div class="account-card">
                            <h2 class="card-title">My Wishlist</h2>

                            <?php if (empty($wishlist)): ?>
                            <div class="empty-wishlist">
                                <p>Your wishlist is empty.</p>
                                <a href="<?php echo url('/views/home/shop.php'); ?>" class="btn-primary continue-shopping">Continue Shopping</a>
                            </div>
                            <?php else: ?>
                            <div class="wishlist-items">
                                <?php foreach ($wishlist as $item): ?>
                                <div class="wishlist-item">
                                    <div class="product-image">
                                        <a href="<?php echo url('/views/home/product.php?id=' . $item['product_id']); ?>">
                                            <img src="<?php echo url('/public/images/' . $item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="img-fluid">
                                        </a>
                                    </div>

                                    <div class="product-info">
                                        <h3 class="product-title">
                                            <a href="<?php echo url('/views/home/product.php?id=' . $item['product_id']); ?>">
                                                <?php echo htmlspecialchars($item['name']); ?>
                                            </a>
                                        </h3>
                                        <p class="product-price"><?php echo number_format($item['price'], 2); ?>DH</p>
                                    </div>

                                    <div class="product-actions">
                                        <form action="<?php echo url('/controllers/cart/add.php'); ?>" method="post" class="add-to-cart-form">
                                            <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                            <button type="submit" class="btn-add-to-cart">Add to Cart</button>
                                        </form>

                                        <form action="<?php echo url('/controllers/wishlist/remove.php'); ?>" method="post" class="remove-form">
                                            <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                            <button type="submit" class="btn-remove">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Change Password Tab -->
                    <div class="tab-pane fade" id="password">
                        <div class="account-card">
                            <h2 class="card-title">Change Password</h2>

                            <?php if (isset($_SESSION['password_success'])): ?>
                            <div class="alert alert-success">
                                <?php echo $_SESSION['password_success']; ?>
                                <?php unset($_SESSION['password_success']); ?>
                            </div>
                            <?php endif; ?>

                            <?php if (isset($_SESSION['password_errors'])): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($_SESSION['password_errors'] as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <?php unset($_SESSION['password_errors']); ?>
                            </div>
                            <?php endif; ?>

                            <form action="<?php echo url('/controllers/user/update-password.php'); ?>" method="post" class="password-form">
                                <div class="form-group">
                                    <label for="current_password">Current Password</label>
                                    <input type="password" id="current_password" name="current_password" required>
                                </div>

                                <div class="form-group">
                                    <label for="new_password">New Password</label>
                                    <input type="password" id="new_password" name="new_password" required>
                                </div>

                                <div class="form-group">
                                    <label for="confirm_password">Confirm New Password</label>
                                    <input type="password" id="confirm_password" name="confirm_password" required>
                                </div>

                                <button type="submit" class="btn-primary">Update Password</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Simple JavaScript for quantity selector in cart tab -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const minusBtns = document.querySelectorAll('.quantity-btn.minus');
    const plusBtns = document.querySelectorAll('.quantity-btn.plus');

    // Connect the header update button to the cart items form
    const updateCartBtn = document.querySelector('#cart-update-form button');
    const cartItemsForm = document.querySelector('#cart-items-form');

    if (updateCartBtn && cartItemsForm) {
        updateCartBtn.addEventListener('click', function(e) {
            e.preventDefault();
            cartItemsForm.submit();
        });
    }

    minusBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            const input = this.parentNode.querySelector('input');
            let currentValue = parseInt(input.value);
            if (currentValue > 1) {
                input.value = currentValue - 1;
                // Auto-submit the form when quantity changes
                if (cartItemsForm) {
                    cartItemsForm.submit();
                }
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
                // Auto-submit the form when quantity changes
                if (cartItemsForm) {
                    cartItemsForm.submit();
                }
            }
        });
    });

    // Also handle direct input changes
    const quantityInputs = document.querySelectorAll('.quantity-input input[type="number"]');
    quantityInputs.forEach(function(input) {
        input.addEventListener('change', function() {
            // Auto-submit the form when quantity changes
            if (cartItemsForm) {
                cartItemsForm.submit();
            }
        });
    });

    // Check if there's a hash in the URL and activate the corresponding tab
    const hash = window.location.hash;
    if (hash) {
        const tabId = hash.substring(1); // Remove the # character
        const tabElement = document.querySelector(`a[href="#${tabId}"]`);
        if (tabElement) {
            // Use Bootstrap's tab method to show the tab
            const tab = new bootstrap.Tab(tabElement);
            tab.show();
        }
    }
});
</script>

<?php
// Include footer
include_once '../../includes/footer.php';
?>

