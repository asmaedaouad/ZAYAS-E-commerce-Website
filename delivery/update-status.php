<?php
// Include configuration
require_once '../config/config.php';
require_once '../config/Database.php';
require_once './controllers/DeliveryController.php';

// Check if user is logged in and is delivery personnel
if (!isLoggedIn() || !isDelivery()) {
    redirect('/views/auth/login.php');
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/delivery/dashboard.php');
}

// Get delivery ID and status
$deliveryId = isset($_POST['delivery_id']) ? (int)$_POST['delivery_id'] : 0;
$status = isset($_POST['status']) ? sanitize($_POST['status']) : '';

// Validate input
if ($deliveryId <= 0 || empty($status)) {
    $_SESSION['error_message'] = 'Invalid input. Please try again.';
    redirect('/delivery/dashboard.php');
}

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Create delivery controller
$deliveryController = new DeliveryController($db);

// Update status
$result = $deliveryController->updateDeliveryStatus();

// Check result and set message
if (isset($result['success'])) {
    $_SESSION['success_message'] = $result['success'];
} else {
    $errorMessage = 'Failed to update status.';
    if (isset($result['errors']) && !empty($result['errors'])) {
        $errorMessage = implode(', ', $result['errors']);
    }
    $_SESSION['error_message'] = $errorMessage;
}

// Redirect back to dashboard
redirect('/delivery/dashboard.php');
?>
