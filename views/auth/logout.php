<?php
// Include configuration
require_once '../../config/config.php';
require_once '../../config/Database.php';
require_once '../../controllers/AuthController.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Create auth controller
$authController = new AuthController($db);

// Handle logout
$authController->logout();

// Redirect to home page (this should not be reached as logout redirects)
redirect('/index.php');
?>
