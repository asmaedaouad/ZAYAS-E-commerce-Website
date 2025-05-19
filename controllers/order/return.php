<?php
// Include configuration
require_once '../../config/config.php';
require_once '../../config/Database.php';
require_once '../../models/OrderModel.php';
require_once '../../models/DeliveryModel.php';
require_once '../../models/ProductModel.php';

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

// Check if order can be returned (only delivered orders can be returned)
if (strtolower($delivery['delivery_status']) !== 'delivered') {
    // Set error message
    $_SESSION['order_error'] = 'Only delivered orders can be returned';
    redirect('/views/user/account.php#orders');
}

// Create product model
$productModel = new ProductModel($db);

// Get order items
$orderItems = $orderModel->getOrderItems($orderId);

// Update product quantities (increase them back)
foreach ($orderItems as $item) {
    $productId = $item['product_id'];
    $quantity = $item['quantity'];

    // Increase product quantity (add back the returned items)
    $productModel->updateProductQuantity($productId, $quantity);
}

// Update order status to returned
$orderModel->updateOrderStatus($orderId, 'returned');

// Update delivery status to returned
$deliveryModel->updateDeliveryStatus($delivery['id'], 'returned');

// Set success message
$_SESSION['order_success'] = 'Return request has been submitted successfully';

// Redirect back to orders page
redirect('/views/user/account.php#orders');
?>

