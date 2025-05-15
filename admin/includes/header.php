<?php
// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('/views/auth/login.php');
}

// Get current date
$currentDate = date('F d, Y');

// Get current page for active menu highlighting
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - ' : ''; ?>ZAYAS Admin</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo url('/admin/assets/css/admin.css'); ?>">

    <?php if (isset($customCss)): ?>
    <link rel="stylesheet" href="<?php echo url('/admin/assets/css/' . $customCss); ?>">
    <?php endif; ?>
</head>
<body>
    <!-- Logout confirmation script is loaded in footer.php -->
    <div class="admin-container">
        <!-- Sidebar Overlay -->
        <div class="sidebar-overlay"></div>

        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h3>ZAYAS Admin</h3>
            </div>
            <ul class="sidebar-menu">
                <li class="<?php echo $currentPage === 'dashboard' ? 'active' : ''; ?>">
                    <a href="<?php echo url('/admin/dashboard.php'); ?>">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                <li class="<?php echo $currentPage === 'products' ? 'active' : ''; ?>">
                    <a href="<?php echo url('/admin/products.php'); ?>">
                        <i class="fas fa-box"></i> Products
                    </a>
                </li>
                <li class="<?php echo $currentPage === 'orders' ? 'active' : ''; ?>">
                    <a href="<?php echo url('/admin/orders.php'); ?>">
                        <i class="fas fa-shopping-cart"></i> Orders
                    </a>
                </li>
                <li class="<?php echo $currentPage === 'customers' ? 'active' : ''; ?>">
                    <a href="<?php echo url('/admin/customers.php'); ?>">
                        <i class="fas fa-users"></i> Customers
                    </a>
                </li>
                <li class="<?php echo $currentPage === 'delivery-personnel' ? 'active' : ''; ?>">
                    <a href="<?php echo url('/admin/delivery-personnel.php'); ?>">
                        <i class="fas fa-truck"></i> Delivery Personnel
                    </a>
                </li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Header -->
            <div class="top-header">
                <!-- Sidebar Toggle Button for Mobile -->
                <button id="sidebarToggle" class="sidebar-toggle d-md-none">
                    <i class="fas fa-bars"></i>
                </button>

                <div class="date">
                    <i class="far fa-calendar-alt"></i> <?php echo $currentDate; ?>
                </div>
                <div class="store-link">
                    <a href="<?php echo url('/index.php'); ?>" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-store"></i> Go to Store
                    </a>
                </div>
                <div class="admin-profile dropdown">
                    <a class="dropdown-toggle" href="#" role="button" id="adminDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminDropdown">
                        <li><a class="dropdown-item" href="<?php echo url('/admin/profile.php'); ?>"><i class="fas fa-user"></i> Profile</a></li>
                        <li><a class="dropdown-item" href="<?php echo url('/admin/settings.php'); ?>"><i class="fas fa-cog"></i> Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item logout-link" href="javascript:void(0);" onclick="confirmLogout('<?php echo url('/views/auth/logout.php'); ?>')"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    </ul>
                </div>
            </div>

            <!-- Page Content -->
            <div class="page-content">
                <div class="page-header">
                    <h1><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Dashboard'; ?></h1>
                </div>

