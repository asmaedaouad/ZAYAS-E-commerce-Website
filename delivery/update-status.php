<?php

require_once '../config/config.php';
require_once '../config/Database.php';
require_once './controllers/DeliveryController.php';


if (!isLoggedIn() || !isDelivery()) {
    redirect('/views/auth/login.php');
}


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/delivery/dashboard.php');
}


$deliveryId = isset($_POST['delivery_id']) ? (int)$_POST['delivery_id'] : 0;
$status = isset($_POST['status']) ? sanitize($_POST['status']) : '';


if ($deliveryId <= 0 || empty($status)) {
    $_SESSION['error_message'] = 'Invalid input. Please try again.';
    redirect('/delivery/dashboard.php');
}


$database = new Database();
$db = $database->getConnection();


$deliveryController = new DeliveryController($db);


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


redirect('/delivery/dashboard.php');
?>
