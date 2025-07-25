<?php

require_once '../../config/config.php';
require_once '../../config/Database.php';
require_once './AdminDeliveryController.php';
require_once './AdminOrderController.php';


if (!isLoggedIn() || !isAdmin()) {
    redirect('/views/auth/login.php');
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $orderId = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
    $personnelId = isset($_POST['personnel_id']) ? intval($_POST['personnel_id']) : 0;

    
    if ($orderId <= 0 || $personnelId <= 0) {
        $_SESSION['error_message'] = 'Invalid order or delivery personnel';
        redirect('/admin/orders.php');
    }

    
    $database = new Database();
    $db = $database->getConnection();

    
    $deliveryController = new AdminDeliveryController($db);
    $orderController = new AdminOrderController($db);

    
    $deliveryInfo = $orderController->getDeliveryInfo($orderId);

    if (!$deliveryInfo) {
        $_SESSION['error_message'] = 'Delivery information not found for this order';
        redirect('/admin/orders.php');
    }

    
    if ($deliveryController->assignDelivery($deliveryInfo['id'], $personnelId)) {
        
        $orderController->updateOrderStatus($orderId, 'assigned');

        $_SESSION['success_message'] = 'Order successfully assigned to delivery personnel';
    } else {
        $_SESSION['error_message'] = 'Failed to assign order to delivery personnel';
    }

    
    redirect('/admin/orders.php');
} else {
    
    redirect('/admin/orders.php');
}
?>

