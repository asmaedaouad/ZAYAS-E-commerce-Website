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
$pageTitle = 'Shop';
$customCss = 'shop.css';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Create home controller
$homeController = new HomeController($db);
$wishlistController = new WishlistController($db);

// Get shop page data
$data = $homeController->shop();
$products = $data['products'];
$title = $data['title'];
$type = $data['type'];
$search = $data['search'];

// Include header
include_once '../../includes/header.php';
?>

<!-- Shop Banner -->
<section class="collection-banner">
    <div class="container">
        <h1><?php echo htmlspecialchars($title); ?></h1>
        <p>Discover our collection of modest fashion pieces</p>
    </div>
</section>

<!-- Category Bubbles -->
<section class="category-bubbles py-4">
    <div class="container">
        <div class="bubbles-container d-flex justify-content-center flex-wrap">
            <!-- All Products Bubble -->
            <a href="<?php echo url('/views/home/shop.php'); ?>" class="category-bubble mx-3 <?php echo !$type ? 'active' : ''; ?>">
                <div class="bubble-img">
                    <img src="<?php echo url('/public/images/all-products.png'); ?>" alt="All Products">
                </div>
                <p>All Products</p>
            </a>

            <!-- Abayas Bubble -->
            <a href="<?php echo url('/views/home/shop.php?type=abaya'); ?>" class="category-bubble mx-3 <?php echo $type == 'abaya' ? 'active' : ''; ?>">
                <div class="bubble-img">
                    <img src="<?php echo url('/public/images/blacksimpleabaya.png'); ?>" alt="Abayas">
                </div>
                <p>Abayas</p>
            </a>

            <!-- Dresses Bubble -->
            <a href="<?php echo url('/views/home/shop.php?type=dress'); ?>" class="category-bubble mx-3 <?php echo $type == 'dress' ? 'active' : ''; ?>">
                <div class="bubble-img">
                    <img src="<?php echo url('/public/images/bluedress.png'); ?>" alt="Dresses">
                </div>
                <p>Dresses</p>
            </a>

            <!-- Hijabs Bubble -->
            <a href="<?php echo url('/views/home/shop.php?type=hijab'); ?>" class="category-bubble mx-3 <?php echo $type == 'hijab' ? 'active' : ''; ?>">
                <div class="bubble-img">
                    <img src="<?php echo url('/public/images/biegehijab.png'); ?>" alt="Hijabs">
                </div>
                <p>Hijabs</p>
            </a>
        </div>
    </div>
</section>

<!-- Shop Content -->
<section class="shop-content section-padding">
    <div class="container">
        <!-- Products Grid -->
        <div>
                <?php if ($search): ?>
                <div class="search-results-info mb-4">
                    <p>Search results for: <strong><?php echo htmlspecialchars($search); ?></strong></p>
                </div>
                <?php endif; ?>

                <?php if (empty($products)): ?>
                <div class="no-products-found">
                    <p>No products found.</p>
                </div>
                <?php else: ?>
                <div class="row">
                    <?php foreach ($products as $product): ?>
                    <div class="col-6 col-md-4 col-lg-3 mb-4">
                        <div class="product-card">
                            <?php if ($product['quantity'] <= 0): ?>
                            <span class="badge bg-danger">Out of Stock</span>
                            <?php elseif ($product['is_new']): ?>
                            <span class="badge bg-success">New</span>
                            <?php endif; ?>

                            <div class="product-image-container">
                                <a href="<?php echo url('/views/home/product.php?id=' . $product['id']); ?>" class="product-image">
                                    <img src="<?php echo url('/public/images/' . $product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="img-fluid">
                                </a>

                                <!-- Wishlist heart icon at the top -->
                                <div class="wishlist-icon">
                                    <?php if (isLoggedIn() && !isAdmin() && !isDelivery()): ?>
                                    <?php $isInWishlist = $wishlistController->isInWishlist($product['id']); ?>
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
                            </div>

                            <div class="product-info">
                                <h3 class="product-title">
                                    <a href="<?php echo url('/views/home/product.php?id=' . $product['id']); ?>"><?php echo htmlspecialchars($product['name']); ?></a>
                                </h3>
                                <p class="product-type"><?php echo ucfirst(htmlspecialchars($product['type'])); ?></p>

                                <div class="product-price">
                                    <?php if (isset($product['old_price']) && $product['old_price'] > 0): ?>
                                    <span class="old-price"><?php echo number_format($product['old_price'], 2); ?>DH</span>
                                    <?php endif; ?>
                                    <span class="current-price"><?php echo number_format($product['price'], 2); ?>DH</span>
                                </div>

                                <div class="product-actions">
                                    <?php if (isLoggedIn() && !isAdmin() && !isDelivery()): ?>
                                        <?php if ($product['quantity'] <= 0): ?>
                                        <button type="button" class="btn-add-to-cart disabled" disabled>Out of Stock</button>
                                        <?php else: ?>
                                        <form action="<?php echo url('/controllers/cart/add.php'); ?>" method="post">
                                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                            <button type="submit" class="btn-add-to-cart">Add to Cart</button>
                                        </form>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?php if ($product['quantity'] <= 0): ?>
                                        <button type="button" class="btn-add-to-cart disabled" disabled>Out of Stock</button>
                                        <?php else: ?>
                                        <a href="<?php echo url('/views/auth/login.php'); ?>" class="btn-add-to-cart">Add to Cart</a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
        </div>
    </div>
</section>

<?php
// Include footer
include_once '../../includes/footer.php';
?>

<!-- Shop Page JavaScript -->
<script src="<?php echo url('/public/js/shop.js'); ?>"></script>




