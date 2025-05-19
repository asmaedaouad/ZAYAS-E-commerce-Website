<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>ZAYAS Delivery</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo url('/delivery/public/css/style.css'); ?>">
    <?php if (isset($customCss)): ?>
    <link rel="stylesheet" href="<?php echo url('/delivery/public/css/' . $customCss); ?>">
    <?php endif; ?>
</head>
<body>
    <!-- Logout confirmation script is loaded in footer.php -->
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-brown">
        <div class="container">
            <a class="navbar-brand" href="<?php echo url('/delivery/dashboard.php'); ?>">
                <i class="fas fa-truck me-2"></i>ZAYAS Delivery
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <?php if (isLoggedIn() && isDelivery()): ?>
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo url('/delivery/dashboard.php'); ?>">Dashboard</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-1"></i> <?php echo $_SESSION['first_name']; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="<?php echo url('/delivery/views/profile.php'); ?>">Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item logout-link" href="javascript:void(0);" onclick="confirmLogout('<?php echo url('/views/auth/logout.php'); ?>')">Logout</a>
                            </li>
                        </ul>
                    </li>
                </ul>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container py-4">
        <?php if (isset($pageTitle)): ?>
        <div class="row mb-4">
            <div class="col-md-12">
                <h1 class="page-title"><?php echo $pageTitle; ?></h1>
            </div>
        </div>
        <?php endif; ?>
