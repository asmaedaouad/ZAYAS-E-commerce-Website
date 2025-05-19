<?php
// Check if BASE_DIR is defined (when included from index.php)
if (!defined('BASE_DIR')) {
    // When accessed directly
    require_once '../../config/config.php';
    require_once '../../config/Database.php';
    require_once '../../controllers/HomeController.php';
    require_once '../../controllers/WishlistController.php';

    // Check if user is delivery personnel and redirect to logout
    if (isLoggedIn() && isDelivery()) {
        // Logout delivery personnel who try to access the store
        redirect('/views/auth/logout.php');
    }
} else {
    // When included from index.php
    require_once BASE_DIR . '/config/Database.php';
    require_once BASE_DIR . '/controllers/HomeController.php';
    require_once BASE_DIR . '/controllers/WishlistController.php';
}

// Set page title
$pageTitle = 'Home';
$customCss = 'home.css';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Create home controller
$homeController = new HomeController($db);
$wishlistController = new WishlistController($db);

// Get home page data
$data = $homeController->index();
$newArrivals = $data['new_arrivals'];
$featuredProducts = $data['featured_products'];

// Include header
if (!defined('BASE_DIR')) {
    include_once '../../includes/header.php';
} else {
    include_once BASE_DIR . '/includes/header.php';
}
?>

<!-- Hero Section with Video and Slider -->
<div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
        <!-- Video Slide -->
        <div class="carousel-item active">
            <div class="hero-slide">
                <video autoplay muted loop class="hero-video">
                    <source src="<?php echo url('/public/videos/fashion.mp4'); ?>" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
                <div class="hero-content">
                    <div class="container">
                        <h1>Elegant Modest Fashion</h1>
                        <p>Discover our new collection of modest fashion pieces</p>
                        <a href="<?php echo url('/views/home/shop.php'); ?>" class="btn btn-outline-light btn-lg">Shop Now</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Image Slide 1 -->
        <div class="carousel-item">
            <div class="hero-slide" style="background-image: url('<?php echo url('/public/images/slider1.png'); ?>');">
                <div class="hero-content">
                    <div class="container">
                        <h1>Timeless Elegance</h1>
                        <p>Explore our premium collection of modest wear</p>
                        <a href="<?php echo url('/views/home/shop.php?type=abaya'); ?>" class="btn btn-outline-light btn-lg">Shop Abayas</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Image Slide 2 -->
        <div class="carousel-item">
            <div class="hero-slide" style="background-image: url('<?php echo url('/public/images/about-hero.jpg'); ?>');">
                <div class="hero-content">
                    <div class="container">
                        <h1>Modern Modest Style</h1>
                        <p>Fashion that respects tradition while embracing modernity</p>
                        <a href="<?php echo url('/views/home/shop.php?type=dress'); ?>" class="btn btn-outline-light btn-lg">Shop Dresses</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Carousel Controls -->
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>

    <!-- Carousel Indicators -->
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
    </div>
</div>

<!-- Categories Section -->
<section class="categories-section">
    <div class="container">
        <h2 class="section-title text-center">Shop by Category</h2>
        <div class="row">
            <!-- Abaya Category -->
            <div class="col-md-4">
                <div class="category-card">
                    <a href="<?php echo url('/views/home/shop.php?type=abaya'); ?>">
                        <div class="category-img">
                            <img src="<?php echo url('/public/images/blacksimpleabaya.png'); ?>" alt="Abayas" class="img-fluid">
                        </div>
                        <div class="category-info">
                            <h3>Abayas</h3>
                            <p>Elegant & Modest</p>
                            <span class="btn-shop">Shop Now</span>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Dress Category -->
            <div class="col-md-4">
                <div class="category-card">
                    <a href="<?php echo url('/views/home/shop.php?type=dress'); ?>">
                        <div class="category-img">
                            <img src="<?php echo url('/public/images/bluedress.png'); ?>" alt="Dresses" class="img-fluid">
                        </div>
                        <div class="category-info">
                            <h3>Dresses</h3>
                            <p>Stylish & Comfortable</p>
                            <span class="btn-shop">Shop Now</span>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Hijab Category -->
            <div class="col-md-4">
                <div class="category-card">
                    <a href="<?php echo url('/views/home/shop.php?type=hijab'); ?>">
                        <div class="category-img">
                            <img src="<?php echo url('/public/images/biegehijab.png'); ?>" alt="Hijabs" class="img-fluid">
                        </div>
                        <div class="category-info">
                            <h3>Hijabs</h3>
                            <p>Beautiful & Versatile</p>
                            <span class="btn-shop">Shop Now</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- New Arrivals Section -->
<section class="new-arrivals-section">
    <div class="container">
        <h2 class="section-title text-center">New Arrivals</h2>
        <div class="row">
            <?php foreach ($newArrivals as $product): ?>
            <div class="col-6 col-md-3">
                <div class="product-card">
                    <div class="product-img">
                        <a href="<?php echo url('/views/home/product.php?id=' . $product['id']); ?>">
                            <img src="<?php echo url('/public/images/' . $product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="img-fluid">
                        </a>
                        <?php if ($product['quantity'] <= 0): ?>
                        <span class="badge bg-danger product-badge">Out of Stock</span>
                        <?php elseif ($product['is_new']): ?>
                        <span class="badge bg-success product-badge">New</span>
                        <?php endif; ?>
                        <!-- Wishlist heart icon at the top -->
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
                    <div class="product-info">
                        <h3><a href="<?php echo url('/views/home/product.php?id=' . $product['id']); ?>"><?php echo htmlspecialchars($product['name']); ?></a></h3>
                        <p class="product-type"><?php echo ucfirst(htmlspecialchars($product['type'])); ?></p>
                        <div class="product-price">
                            <?php if (isset($product['old_price']) && $product['old_price'] > 0): ?>
                            <span class="old-price"><?php echo number_format($product['old_price'], 2); ?>DH</span>
                            <?php endif; ?>
                            <span class="price"><?php echo number_format($product['price'], 2); ?>DH</span>
                        </div>
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
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section class="featured-products-section">
    <div class="container">
        <h2 class="section-title text-center">Featured Products</h2>
        <div class="row">
            <?php foreach ($featuredProducts as $product): ?>
            <div class="col-6 col-md-3">
                <div class="product-card">
                    <div class="product-img">
                        <a href="<?php echo url('/views/home/product.php?id=' . $product['id']); ?>">
                            <img src="<?php echo url('/public/images/' . $product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="img-fluid">
                        </a>
                        <?php if ($product['quantity'] <= 0): ?>
                        <span class="badge bg-danger product-badge">Out of Stock</span>
                        <?php elseif ($product['is_new']): ?>
                        <span class="badge bg-success product-badge">New</span>
                        <?php endif; ?>
                        <!-- Wishlist heart icon at the top -->
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
                    <div class="product-info">
                        <h3><a href="<?php echo url('/views/home/product.php?id=' . $product['id']); ?>"><?php echo htmlspecialchars($product['name']); ?></a></h3>
                        <p class="product-type"><?php echo ucfirst(htmlspecialchars($product['type'])); ?></p>
                        <div class="product-price">
                            <?php if (isset($product['old_price']) && $product['old_price'] > 0): ?>
                            <span class="old-price"><?php echo number_format($product['old_price'], 2); ?>DH</span>
                            <?php endif; ?>
                            <span class="price"><?php echo number_format($product['price'], 2); ?>DH</span>
                        </div>
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
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- JavaScript for Product Interactions -->
<script>
// Add event listeners when the DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize any interactive elements
    console.log('DOM fully loaded and parsed');
});
</script>

<?php
// Include footer
if (!defined('BASE_DIR')) {
    include_once '../../includes/footer.php';
} else {
    include_once BASE_DIR . '/includes/footer.php';
}
?>

