<?php

require_once '../config/config.php';

// Redirect to login if not logged in
if (!isLoggedIn() || !isAdmin()) {
    redirect('/views/auth/login.php');
}


redirect('/admin/dashboard.php');
?>

