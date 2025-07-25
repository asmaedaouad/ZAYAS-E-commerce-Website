<?php

define('BASE_URL', '/ZAYAS_simple');
define('SITE_NAME', 'ZAYAS');


session_start();


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


function redirect($url) {
    header('Location: ' . BASE_URL . $url);
    exit;
}

function url($path) {
    return BASE_URL . $path;
}


function isLoggedIn() {
    return isset($_SESSION['user_id']);
}


function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
}


function isDelivery() {
    return isset($_SESSION['is_delivery']) && $_SESSION['is_delivery'] == 1;
}


function sanitize($input) {
    
    $input = trim($input);

    
    $input = strip_tags($input);

   
    $input = htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');

    return $input;
}
?>
