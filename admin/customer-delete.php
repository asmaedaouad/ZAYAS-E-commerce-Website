<?php

require_once '../config/config.php';
require_once '../config/Database.php';
require_once './controllers/AdminCustomerController.php';


if (!isLoggedIn() || !isAdmin()) {
    redirect('/views/auth/login.php');
}


if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect('/admin/customers.php');
}

$customerId = (int)$_GET['id'];


$database = new Database();
$db = $database->getConnection();


$customerController = new AdminCustomerController($db);


$customer = $customerController->getCustomerById($customerId);

if (!$customer) {
    redirect('/admin/customers.php');
}


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


redirect('/admin/customers.php');
?>
