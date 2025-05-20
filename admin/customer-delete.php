<?php
// Include configuration
require_once '../config/config.php';
require_once '../config/Database.php';
require_once './controllers/AdminCustomerController.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('/views/auth/login.php');
}

// Check if customer ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect('/admin/customers.php');
}

$customerId = (int)$_GET['id'];

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Create customer controller
$customerController = new AdminCustomerController($db);

// Get customer to check if it exists
$customer = $customerController->getCustomerById($customerId);

if (!$customer) {
    redirect('/admin/customers.php');
}

// Delete customer
if ($customerController->deleteCustomer($customerId)) {
    // Set success message in session
    $_SESSION['admin_message'] = [
        'type' => 'success',
        'text' => 'Customer deleted successfully.'
    ];
} else {
    // Set error message in session
    $_SESSION['admin_message'] = [
        'type' => 'danger',
        'text' => 'Failed to delete customer.'
    ];
}

// Redirect back to customers page
redirect('/admin/customers.php');
?>
