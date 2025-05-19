<?php
// Include configuration
require_once '../../config/config.php';
require_once '../../config/Database.php';
require_once '../../controllers/HomeController.php';
require_once '../../controllers/WishlistController.php';

// Check if user is delivery personnel and redirect to logout
if (isLoggedIn() && isDelivery()) {
    // Logout delivery personnel who try to access the store
    redirect('/views/auth/logout.php');
}

// Set page title
$customCss = 'product.css';

// Get product ID
$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($productId <= 0) {
    redirect('/views/home/shop.php');
}

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Create controllers
$homeController = new HomeController($db);
$wishlistController = new WishlistController($db);

// Get product details
$data = $homeController->productDetails($productId);

if (isset($data['error'])) {
    redirect('/views/home/shop.php');
}

$product = $data['product'];
$relatedProducts = $data['related_products'];

// Check if product is in wishlist
$isInWishlist = isLoggedIn() ? $wishlistController->isInWishlist($productId) : false;

// Set page title
$pageTitle = $product['name'];

// Include header
include_once '../../includes/header.php';
?>

<!-- Product Details Section -->
<section class="product-details section-padding">
    <div class="container">
        <div class="row">
            <!-- Product Image -->
            <div class="col-md-6">
                <div class="product-image">
                    <img src="<?php echo url('/public/images/' . $product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="img-fluid">
                    <!-- Wishlist heart icon at the top -->
                    <div class="wishlist-icon">
                        <?php if (isLoggedIn() && !isAdmin() && !isDelivery()): ?>
                        <form action="<?php echo url('/controllers/wishlist/' . ($isInWishlist ? 'remove' : 'add') . '.php'); ?>" method="post">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <button type="submit" class="btn-wishlist <?php echo $isInWishlist ? 'active' : ''; ?>">
                                <i class="<?php echo $isInWishlist ? 'fas' : 'far'; ?> fa-heart"></i>
                            </button>
                        </form>
                        <?php else: ?>
                        <a href="<?php echo url('/views/auth/login.php'); ?>" class="btn-wishlist">
                            <i class="far fa-heart"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                    <?php if ($product['quantity'] <= 0): ?>
                    <span class="product-badge out-of-stock">Out of Stock</span>
                    <?php elseif ($product['is_new']): ?>
                    <span class="product-badge new">New</span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Product Info -->
            <div class="col-md-6">
                <div class="product-info">
                    <h1 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h1>
                    <p class="product-type"><?php echo ucfirst(htmlspecialchars($product['type'])); ?></p>

                    <div class="product-price">
                        <?php if (isset($product['old_price']) && $product['old_price'] > 0): ?>
                        <span class="old-price"><?php echo number_format($product['old_price'], 2); ?>DH</span>
                        <?php endif; ?>
                        <span class="current-price"><?php echo number_format($product['price'], 2); ?>DH</span>
                    </div>

                    <div class="product-description">
                        <p><?php echo htmlspecialchars($product['description']); ?></p>
                    </div>

                    <div class="product-status">
                        <p class="availability">
                            Availability:
                            <?php if ($product['quantity'] > 0): ?>
                            <span class="in-stock">In Stock</span>
                            <?php else: ?>
                            <span class="out-of-stock">Out of Stock</span>
                            <?php endif; ?>
                        </p>
                    </div>

                    <div class="product-actions">
                        <?php if (isLoggedIn() && !isAdmin() && !isDelivery()): ?>
                        <form action="<?php echo url('/controllers/cart/add.php'); ?>" method="post" class="add-to-cart-form">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">

                            <div class="quantity-selector">
                                <label for="quantity">Quantity:</label>
                                <div class="quantity-input">
                                    <button type="button" class="quantity-btn minus">-</button>
                                    <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?php echo $product['quantity']; ?>">
                                    <button type="button" class="quantity-btn plus">+</button>
                                </div>
                            </div>

                            <div class="action-buttons">
                                <?php if ($product['quantity'] <= 0): ?>
                                <button type="button" class="btn-add-to-cart disabled" disabled>
                                    Out of Stock
                                </button>
                                <?php else: ?>
                                <button type="submit" class="btn-add-to-cart">
                                    Add to Cart
                                </button>
                                <?php endif; ?>
                            </div>
                        </form>
                        <?php else: ?>
                        <div class="action-buttons">
                            <?php if ($product['quantity'] <= 0): ?>
                            <button type="button" class="btn-add-to-cart disabled" disabled>
                                Out of Stock
                            </button>
                            <?php else: ?>
                            <a href="<?php echo url('/views/auth/login.php'); ?>" class="btn-add-to-cart">
                                Add to Cart
                            </a>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related Products Section -->
<section class="related-products section-padding">
    <div class="container">
        <h2 class="section-title">Related Products</h2>
        <p class="section-subtitle">You might also like</p>

        <div class="row">
            <?php foreach ($relatedProducts as $relatedProduct): ?>
            <?php if ($relatedProduct['id'] != $product['id']): ?>
            <div class="col-6 col-md-3 mb-4">
                <div class="product-card">
                    <?php if ($relatedProduct['quantity'] <= 0): ?>
                    <span class="product-badge out-of-stock">Out of Stock</span>
                    <?php elseif ($relatedProduct['is_new']): ?>
                    <span class="product-badge new">New</span>
                    <?php endif; ?>

                    <div class="product-image-container">
                        <a href="<?php echo url('/views/home/product.php?id=' . $relatedProduct['id']); ?>" class="product-image">
                            <img src="<?php echo url('/public/images/' . $relatedProduct['image_path']); ?>" alt="<?php echo htmlspecialchars($relatedProduct['name']); ?>" class="img-fluid">
                        </a>

                        <!-- Wishlist heart icon at the top -->
                        <div class="wishlist-icon">
                            <?php if (isLoggedIn() && !isAdmin() && !isDelivery()): ?>
                            <form action="<?php echo url('/controllers/wishlist/add.php'); ?>" method="post">
                                <input type="hidden" name="product_id" value="<?php echo $relatedProduct['id']; ?>">
                                <button type="submit" class="btn-wishlist">
                                    <i class="far fa-heart"></i>
                                </button>
                            </form>
                            <?php else: ?>
                            <a href="<?php echo url('/views/auth/login.php'); ?>" class="btn-wishlist">
                                <i class="far fa-heart"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="product-info">
                        <h3 class="product-title">
                            <a href="<?php echo url('/views/home/product.php?id=' . $relatedProduct['id']); ?>"><?php echo htmlspecialchars($relatedProduct['name']); ?></a>
                        </h3>
                        <p class="product-type"><?php echo ucfirst(htmlspecialchars($relatedProduct['type'])); ?></p>

                        <div class="product-price">
                            <?php if (isset($relatedProduct['old_price']) && $relatedProduct['old_price'] > 0): ?>
                            <span class="old-price"><?php echo number_format($relatedProduct['old_price'], 2); ?>DH</span>
                            <?php endif; ?>
                            <span class="current-price"><?php echo number_format($relatedProduct['price'], 2); ?>DH</span>
                        </div>

                        <div class="product-actions">
                            <?php if (isLoggedIn() && !isAdmin() && !isDelivery()): ?>
                                <?php if ($relatedProduct['quantity'] <= 0): ?>
                                <button type="button" class="btn-add-to-cart disabled" disabled>Out of Stock</button>
                                <?php else: ?>
                                <form action="<?php echo url('/controllers/cart/add.php'); ?>" method="post">
                                    <input type="hidden" name="product_id" value="<?php echo $relatedProduct['id']; ?>">
                                    <button type="submit" class="btn-add-to-cart">Add to Cart</button>
                                </form>
                                <?php endif; ?>
                            <?php else: ?>
                                <?php if ($relatedProduct['quantity'] <= 0): ?>
                                <button type="button" class="btn-add-to-cart disabled" disabled>Out of Stock</button>
                                <?php else: ?>
                                <a href="<?php echo url('/views/auth/login.php'); ?>" class="btn-add-to-cart">Add to Cart</a>
                                <?php endif; ?>
                            <?php endif; ?>


                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Simple JavaScript for quantity selector -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const minusBtn = document.querySelector('.quantity-btn.minus');
    const plusBtn = document.querySelector('.quantity-btn.plus');
    const quantityInput = document.querySelector('#quantity');
    const maxQuantity = <?php echo $product['quantity']; ?>;

    if (minusBtn && plusBtn && quantityInput) {
        minusBtn.addEventListener('click', function() {
            let currentValue = parseInt(quantityInput.value);
            if (currentValue > 1) {
                quantityInput.value = currentValue - 1;
            }
        });

        plusBtn.addEventListener('click', function() {
            let currentValue = parseInt(quantityInput.value);
            if (currentValue < maxQuantity) {
                quantityInput.value = currentValue + 1;
            }
        });
    }
});
</script>

<?php
// Include footer
include_once '../../includes/footer.php';
?>

