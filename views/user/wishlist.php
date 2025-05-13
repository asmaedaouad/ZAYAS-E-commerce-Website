<?php
// Include configuration
require_once '../../config/config.php';
require_once '../../config/Database.php';
require_once '../../controllers/WishlistController.php';

// Set page title
$pageTitle = 'My Wishlist';
$customCss = 'wishlist.css';

// Redirect if not logged in
if (!isLoggedIn()) {
    redirect('/views/auth/login.php');
}

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Create wishlist controller
$wishlistController = new WishlistController($db);

// Get wishlist data
$data = $wishlistController->viewWishlist();
$wishlist = $data['wishlist'];

// Include header
include_once '../../includes/header.php';
?>

<!-- Wishlist Section -->
<section class="wishlist-section section-padding">
    <div class="container">
        <h1 class="page-title">My Wishlist</h1>
        
        <?php if (empty($wishlist)): ?>
        <div class="empty-wishlist">
            <p>Your wishlist is empty.</p>
            <a href="<?php echo url('/views/home/shop.php'); ?>" class="btn-primary">Continue Shopping</a>
        </div>
        <?php else: ?>
        <div class="wishlist-content">
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
                            <p class="product-type"><?php echo ucfirst(htmlspecialchars($item['type'])); ?></p>
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
</section>

<?php
// Include footer
include_once '../../includes/footer.php';
?>
