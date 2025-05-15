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

// Check if user is delivery personnel
function isDelivery() {
    return isset($_SESSION['is_delivery']) && $_SESSION['is_delivery'] == 1;
}

// Sanitize input - enhanced to prevent HTML/code injection
function sanitize($input) {
    // First trim the input to remove leading/trailing whitespace
    $input = trim($input);

    // Strip HTML and PHP tags
    $input = strip_tags($input);

    // Convert special characters to HTML entities
    // ENT_QUOTES handles both single and double quotes
    // ENT_HTML5 provides HTML5 compatibility
    $input = htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');

    return $input;
}
?>
