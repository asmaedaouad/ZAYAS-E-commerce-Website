<?php
// Check if BASE_DIR is defined (when included from index.php)
if (!defined('BASE_DIR')) {
    // When accessed directly
    require_once '../../config/config.php';
    require_once '../../config/Database.php';

    // Include controllers if they exist
    if (file_exists('../../controllers/CartController.php')) {
        require_once '../../controllers/CartController.php';
    }
    if (file_exists('../../controllers/WishlistController.php')) {
        require_once '../../controllers/WishlistController.php';
    }
} else {
    // When included from index.php
    require_once BASE_DIR . '/config/Database.php';

    // Include controllers if they exist
    if (file_exists(BASE_DIR . '/controllers/CartController.php')) {
        require_once BASE_DIR . '/controllers/CartController.php';
    }
    if (file_exists(BASE_DIR . '/controllers/WishlistController.php')) {
        require_once BASE_DIR . '/controllers/WishlistController.php';
    }
}

// Initialize cart and wishlist counts
$cartCount = 0;
$wishlistCount = 0;

// Get cart count if CartController exists
if (class_exists('CartController')) {
    // Create database connection if not already created
    if (!isset($db)) {
        $database = new Database();
        $db = $database->getConnection();
    }
    $cartController = new CartController($db);
    $cartCount = $cartController->getCartCount();
}

// Get wishlist count if user is logged in and WishlistController exists
if (isset($_SESSION['user_id']) && class_exists('WishlistController')) {
    // Create database connection for wishlist
    if (!isset($db)) {
        $database = new Database();
        $db = $database->getConnection();
    }
    $wishlistController = new WishlistController($db);
    $wishlistCount = $wishlistController->getWishlistCount();
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$firstName = $isLoggedIn ? $_SESSION['first_name'] : '';

// Check if user is admin or delivery personnel
$isAdmin = $isLoggedIn && isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
$isDelivery = $isLoggedIn && isset($_SESSION['is_delivery']) && $_SESSION['is_delivery'] == 1;

// Only regular users can access wishlist and cart
$canAccessUserFeatures = $isLoggedIn && !$isAdmin && !$isDelivery;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - ' : ''; ?>ZAYAS</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Base CSS -->
    <link rel="stylesheet" href="<?php echo url('/public/css/header.css'); ?>">
    <link rel="stylesheet" href="<?php echo url('/public/css/footer.css'); ?>">
    <link rel="stylesheet" href="<?php echo url('/public/css/product-card.css'); ?>">

    <!-- Page-specific CSS -->
    <?php if (isset($customCss)): ?>
    <link rel="stylesheet" href="<?php echo url('/public/css/' . $customCss); ?>">
    <?php endif; ?>
</head>
<body>
<header class="header">
    <nav class="navbar navbar-expand-lg navbar-light bg-white">
        <div class="container position-relative">
            <!-- Logo/Brand -->
            <a class="navbar-brand brand-name" href="<?php echo url('/'); ?>">ZAYAS</a>

            <!-- Header Icons for Mobile - Always visible -->
            <div class="mobile-header-icons d-flex d-lg-none align-items-center">
                <div class="search-box me-2">
                    <button class="search-toggle" aria-label="Search">
                        <i class="fas fa-search"></i>
                    </button>
                    <div class="search-input-container">
                        <form action="<?php echo url('/views/home/shop.php'); ?>" method="GET" class="search-form">
                            <i class="fas fa-search search-icon"></i>
                            <input class="form-control" type="search" name="search" placeholder="Search..." aria-label="Search">
                            <i class="fas fa-times close-search"></i>
                        </form>
                    </div>
                </div>

                <div class="header-icons">
                    <?php if (!$isAdmin && !$isDelivery): ?>
                    <a href="<?php echo $canAccessUserFeatures ? url('/views/user/account.php#wishlist') : url('/views/auth/login.php'); ?>" id="wishlist-icon" aria-label="Wishlist" class="icon-link">
                        <i class="far fa-heart"></i>
                        <?php if ($wishlistCount > 0): ?>
                            <span class="wishlist-badge"><?php echo $wishlistCount; ?></span>
                        <?php endif; ?>
                        <span class="icon-tooltip">Wishlist</span>
                    </a>
                    <?php endif; ?>
                    <?php if ($isAdmin): ?>
                    <!-- Admin link for mobile that redirects to admin dashboard -->
                    <a href="<?php echo url('/admin/dashboard.php'); ?>" id="admin-icon" aria-label="Admin Dashboard" class="icon-link">
                        <i class="fas fa-user-shield"></i>
                        <span class="icon-tooltip">Admin</span>
                    </a>
                    <?php else: ?>
                    <a href="<?php echo $isLoggedIn ? url('/views/user/account.php') : url('/views/auth/login.php'); ?>" id="account-icon" aria-label="Account" class="icon-link">
                        <i class="far fa-user"></i>
                        <span class="icon-tooltip">Account</span>
                    </a>
                    <?php endif; ?>
                    <?php if (!$isAdmin && !$isDelivery): ?>
                    <a href="<?php echo $canAccessUserFeatures ? url('/views/user/account.php#cart') : url('/views/auth/login.php'); ?>" id="cart-icon" aria-label="Shopping cart" class="icon-link">
                        <i class="fas fa-shopping-bag"></i>
                        <span class="cart-badge"><?php echo $cartCount; ?></span>
                        <span class="icon-tooltip">Cart</span>
                    </a>
                    <?php endif; ?>
                </div>

                <!-- Burger Menu Toggle -->
                <button class="navbar-toggler ms-3" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>

            <!-- Dark backdrop for mobile menu -->
            <div class="navbar-collapse-backdrop" id="menuBackdrop"></div>

            <!-- Collapsible Navigation Menu -->
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Close button for mobile -->
                <button type="button" class="mobile-menu-close d-lg-none" aria-label="Close menu">
                    <i class="fas fa-times"></i>
                </button>

                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo url('/'); ?>">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo url('/views/home/shop.php'); ?>">Shop</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="collectionsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Collections
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="collectionsDropdown">
                            <li><a class="dropdown-item" href="<?php echo url('/views/home/shop.php?type=abaya'); ?>">Abayas</a></li>
                            <li><a class="dropdown-item" href="<?php echo url('/views/home/shop.php?type=dress'); ?>">Dresses</a></li>
                            <li><a class="dropdown-item" href="<?php echo url('/views/home/shop.php?type=hijab'); ?>">Hijabs</a></li>
                        </ul>
                    </li>
                    <div class="collection-info d-lg-none">
                        <h6>Explore Our Collections</h6>
                        <p>Discover our latest designs crafted with elegance and modesty in mind.</p>
                        <div class="collection-links">
                            <a href="<?php echo url('/views/home/shop.php?type=abaya'); ?>">Abayas</a>
                            <a href="<?php echo url('/views/home/shop.php?type=dress'); ?>">Dresses</a>
                            <a href="<?php echo url('/views/home/shop.php?type=hijab'); ?>">Hijabs</a>
                        </div>
                    </div>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo url('/views/home/about.php'); ?>">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo url('/views/home/contact.php'); ?>">Contact</a>
                    </li>

                    <?php if ($isLoggedIn): ?>
                        <?php if ($isAdmin): ?>
                        <li class="nav-item d-lg-none">
                            <a class="nav-link" href="<?php echo url('/admin/dashboard.php'); ?>">
                                <i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard
                            </a>
                        </li>
                        <?php else: ?>
                        <li class="nav-item d-lg-none">
                            <a class="nav-link" href="<?php echo url('/views/user/account.php'); ?>">My Account</a>
                        </li>
                        <?php endif; ?>
                    <li class="nav-item d-lg-none">
                        <a class="nav-link logout-link" href="javascript:void(0);" onclick="confirmLogout('<?php echo url('/views/auth/logout.php'); ?>')">Logout</a>
                    </li>
                    <?php endif; ?>
                </ul>

                <!-- Desktop Header Icons - Only visible on desktop -->
                <div class="d-none d-lg-flex align-items-center">
                    <div class="search-box">
                        <button class="search-toggle" aria-label="Search">
                            <i class="fas fa-search"></i>
                        </button>
                        <div class="search-input-container">
                            <form action="<?php echo url('/views/home/shop.php'); ?>" method="GET" class="search-form">
                                <i class="fas fa-search search-icon"></i>
                                <input class="form-control" type="search" name="search" placeholder="Search..." aria-label="Search">
                                <i class="fas fa-times close-search"></i>
                            </form>
                        </div>
                    </div>

                    <div class="header-icons">
                        <?php if (!$isAdmin && !$isDelivery): ?>
                        <a href="<?php echo $canAccessUserFeatures ? url('/views/user/account.php#wishlist') : url('/views/auth/login.php'); ?>" id="wishlist-icon-desktop" aria-label="Wishlist" class="icon-link">
                            <i class="far fa-heart"></i>
                            <?php if ($wishlistCount > 0): ?>
                                <span class="wishlist-badge"><?php echo $wishlistCount; ?></span>
                            <?php endif; ?>
                            <span class="icon-tooltip">Wishlist</span>
                        </a>
                        <?php endif; ?>

                        <div class="dropdown">
                            <?php if ($isAdmin): ?>
                            <!-- Admin link that redirects to admin dashboard -->
                            <a href="<?php echo url('/admin/dashboard.php'); ?>" id="admin-icon-desktop" aria-label="Admin Dashboard" class="icon-link">
                                <i class="fas fa-user-shield"></i>
                                <span class="ms-1 d-inline-block username-display">Admin</span>
                                <span class="icon-tooltip">Go to Admin Dashboard</span>
                            </a>
                            <?php else: ?>
                            <a href="<?php echo $isLoggedIn ? url('/views/user/account.php') : url('/views/auth/login.php'); ?>"
                                id="account-icon-desktop" aria-label="Account" <?php echo $isLoggedIn ? 'data-bs-toggle="dropdown"' : ''; ?> class="icon-link">
                                <i class="far fa-user"></i>
                                <?php if ($isLoggedIn): ?>
                                    <span class="ms-1 d-none d-xl-inline-block username-display"><?php echo htmlspecialchars($firstName); ?></span>
                                <?php endif; ?>
                                <span class="icon-tooltip">Account</span>
                            </a>

                            <?php if ($isLoggedIn): ?>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="account-icon-desktop">
                                <li><a class="dropdown-item" href="<?php echo url('/views/user/account.php'); ?>">My Account</a></li>
                                <?php if (!$isAdmin && !$isDelivery): ?>
                                <li><a class="dropdown-item" href="<?php echo url('/views/user/account.php#orders'); ?>">My Orders</a></li>
                                <li><a class="dropdown-item" href="<?php echo url('/views/user/account.php#wishlist'); ?>">My Wishlist</a></li>
                                <li><a class="dropdown-item" href="<?php echo url('/views/user/account.php#cart'); ?>">My Cart</a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item logout-link" href="javascript:void(0);" onclick="confirmLogout('<?php echo url('/views/auth/logout.php'); ?>')">Logout</a></li>
                            </ul>
                            <?php endif; ?>
                            <?php endif; ?>
                        </div>

                        <?php if (!$isAdmin && !$isDelivery): ?>
                        <a href="<?php echo $canAccessUserFeatures ? url('/views/user/account.php#cart') : url('/views/auth/login.php'); ?>" id="cart-icon-desktop" aria-label="Shopping cart" class="icon-link">
                            <i class="fas fa-shopping-bag"></i>
                            <span class="cart-badge"><?php echo $cartCount; ?></span>
                            <span class="icon-tooltip">Cart</span>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>


<!-- Logout confirmation script is loaded in footer.php -->
