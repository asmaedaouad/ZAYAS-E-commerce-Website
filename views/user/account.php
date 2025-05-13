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
                            <a class="nav-link" href="#wishlist" data-bs-toggle="tab">Wishlist</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#password" data-bs-toggle="tab">Change Password</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo url('/views/auth/logout.php'); ?>">Logout</a>
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
                            
                            <?php if (empty($orders)): ?>
                            <div class="empty-orders">
                                <p>You have no orders yet.</p>
                                <a href="<?php echo url('/views/home/shop.php'); ?>" class="btn-primary">Start Shopping</a>
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
                                            <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                            <td>
                                                <span class="order-status <?php echo strtolower($order['status']); ?>">
                                                    <?php echo ucfirst($order['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="<?php echo url('/views/user/order-details.php?id=' . $order['id']); ?>" class="btn-view">
                                                    View Details
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
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
                                <a href="<?php echo url('/views/home/shop.php'); ?>" class="btn-primary">Continue Shopping</a>
                            </div>
                            <?php else: ?>
                            <div class="wishlist-items">
                                <div class="row">
                                    <?php foreach ($wishlist as $item): ?>
                                    <div class="col-md-4 col-sm-6 mb-4">
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
                                                <p class="product-price">$<?php echo number_format($item['price'], 2); ?></p>
                                            </div>
                                            
                                            <div class="product-actions">
                                                <form action="<?php echo url('/controllers/cart/add.php'); ?>" method="post" class="add-to-cart-form">
                                                    <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                                    <button type="submit" class="btn-add-to-cart">Add to Cart</button>
                                                </form>
                                                
                                                <form action="<?php echo url('/controllers/wishlist/remove.php'); ?>" method="post" class="remove-form">
                                                    <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                                    <button type="submit" class="btn-remove">
                                                        <i class="fas fa-times"></i> Remove
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
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

<?php
// Include footer
include_once '../../includes/footer.php';
?>
