<?php
// Include configuration
require_once '../../config/config.php';
require_once '../../config/Database.php';
require_once './AdminDeliveryController.php';
require_once './AdminOrderController.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('/views/auth/login.php');
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $orderId = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
    $personnelId = isset($_POST['personnel_id']) ? intval($_POST['personnel_id']) : 0;

    // Validate input
    if ($orderId <= 0 || $personnelId <= 0) {
        $_SESSION['error_message'] = 'Invalid order or delivery personnel';
        redirect('/admin/orders.php');
    }

    // Get database connection
    $database = new Database();
    $db = $database->getConnection();

    // Create controllers
    $deliveryController = new AdminDeliveryController($db);
    $orderController = new AdminOrderController($db);

    // Get delivery information for the order
    $deliveryInfo = $orderController->getDeliveryInfo($orderId);

    if (!$deliveryInfo) {
        $_SESSION['error_message'] = 'Delivery information not found for this order';
        redirect('/admin/orders.php');
    }

    // Assign delivery to personnel
    if ($deliveryController->assignDelivery($deliveryInfo['id'], $personnelId)) {
        // Update order status to assigned
        $orderController->updateOrderStatus($orderId, 'assigned');

        $_SESSION['success_message'] = 'Order successfully assigned to delivery personnel';
    } else {
        $_SESSION['error_message'] = 'Failed to assign order to delivery personnel';
    }

    // Redirect back to orders page
    redirect('/admin/orders.php');
} else {
    // If not a POST request, redirect to orders page
    redirect('/admin/orders.php');
}
?>

