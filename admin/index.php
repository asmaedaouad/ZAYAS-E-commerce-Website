<?php
// Include configuration
require_once '../config/config.php';

// Redirect to login if not logged in
if (!isLoggedIn() || !isAdmin()) {
    redirect('/views/auth/login.php');
}

// Redirect to dashboard
redirect('/admin/dashboard.php');
?>

