<?php
// Site configuration
define('BASE_URL', '/ZAYAS_simple');
define('SITE_NAME', 'ZAYAS');

// Session configuration
session_start();

// Error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Helper functions
function redirect($url) {
    header('Location: ' . BASE_URL . $url);
    exit;
}

function url($path) {
    return BASE_URL . $path;
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check if user is admin
function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
}

// Sanitize input
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}
?>
