<?php
// Include configuration
require_once '../../config/config.php';
require_once '../../config/Database.php';
require_once '../../models/OrderModel.php';
require_once '../../models/DeliveryModel.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    redirect('/views/auth/login.php');
}

// Check if order ID is provided
if (!isset($_POST['order_id']) || empty($_POST['order_id'])) {
    // Set error message
    $_SESSION['order_error'] = 'Order ID is required';
    redirect('/views/user/account.php#orders');
}

// Get order ID
$orderId = (int)$_POST['order_id'];

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Create order model
$orderModel = new OrderModel($db);
$deliveryModel = new DeliveryModel($db);

// Get order details
$order = $orderModel->getOrderById($orderId);

// Check if order exists and belongs to the current user
if (!$order || $order['user_id'] != $_SESSION['user_id']) {
    // Set error message
    $_SESSION['order_error'] = 'Order not found or access denied';
    redirect('/views/user/account.php#orders');
}

// Get delivery details
$delivery = $deliveryModel->getDeliveryByOrderId($orderId);

// Check if order can be cancelled (only pending or assigned orders can be cancelled)
if (!in_array(strtolower($delivery['delivery_status']), ['pending', 'assigned'])) {
    // Set error message
    $_SESSION['order_error'] = 'Order cannot be cancelled at this stage';
    redirect('/views/user/account.php#orders');
}

// Update order status to cancelled
$orderModel->updateOrderStatus($orderId, 'cancelled');

// Update delivery status to cancelled
$deliveryModel->updateDeliveryStatus($delivery['id'], 'cancelled');

// Set success message
$_SESSION['order_success'] = 'Order has been cancelled successfully';

// Redirect back to orders page
redirect('/views/user/account.php#orders');
?>

