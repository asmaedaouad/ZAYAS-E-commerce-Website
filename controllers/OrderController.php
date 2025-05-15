<?php
require_once __DIR__ . '/../models/OrderModel.php';
require_once __DIR__ . '/../models/DeliveryModel.php';
require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../controllers/CartController.php';

class OrderController {
    private $orderModel;
    private $deliveryModel;
    private $productModel;
    private $cartController;

    public function __construct($db) {
        $this->orderModel = new OrderModel($db);
        $this->deliveryModel = new DeliveryModel($db);
        $this->productModel = new ProductModel($db);
        $this->cartController = new CartController($db);
    }

    // Create new order
    public function createOrder() {
        if (!isLoggedIn()) {
            redirect('/views/auth/login.php');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get cart items
            $cart = $this->cartController->getCartWithProducts();

            if (empty($cart['items'])) {
                redirect('/views/user/cart.php');
            }

            // Get form data
            $address = isset($_POST['address']) ? sanitize($_POST['address']) : '';
            $city = isset($_POST['city']) ? sanitize($_POST['city']) : '';
            $postalCode = isset($_POST['postal_code']) ? sanitize($_POST['postal_code']) : '';
            $phone = isset($_POST['phone']) ? sanitize($_POST['phone']) : '';
            $notes = isset($_POST['notes']) ? sanitize($_POST['notes']) : '';

            // Validate input
            $errors = [];

            if (empty($address)) {
                $errors[] = 'Address is required';
            }

            if (empty($city)) {
                $errors[] = 'City is required';
            }

            if (empty($postalCode)) {
                $errors[] = 'Postal code is required';
            }

            if (empty($phone)) {
                $errors[] = 'Phone is required';
            }

            // Validate cart items
            if (count($cart['items']) === 0) {
                $errors[] = 'Your cart is empty';
                redirect('/views/user/cart.php');
            }

            // If no errors, create order
            if (empty($errors)) {
                // Prepare order items
                $orderItems = [];
                foreach ($cart['items'] as $item) {
                    // Check if product is in stock
                    $product = $this->productModel->getProductById($item['product']['id']);
                    if ($product && $product['quantity'] >= $item['quantity']) {
                        $orderItems[$item['product']['id']] = [
                            'quantity' => $item['quantity'],
                            'price' => $item['product']['price']
                        ];
                    } else {
                        // Product is out of stock or not enough quantity
                        $errors[] = 'Product "' . $item['product']['name'] . '" is not available in the requested quantity';
                        continue;
                    }
                }

                if (empty($errors)) {
                    // Create order - all items in one order
                    $orderId = $this->orderModel->createOrder($_SESSION['user_id'], $orderItems, $cart['total_price']);

                    if ($orderId) {
                        // Create delivery record
                        $deliveryId = $this->deliveryModel->createDelivery($orderId, $address, $city, $postalCode, $phone, $notes);

                        if ($deliveryId) {
                            // Clear cart after successful order
                            $this->cartController->clearCart();

                            // Redirect to order confirmation
                            redirect('/views/user/order-confirmation.php?id=' . $orderId);
                        } else {
                            $errors[] = 'Failed to create delivery record';
                        }
                    } else {
                        $errors[] = 'Failed to create order';
                    }
                }
            }

            // If we get here, there were errors
            return [
                'errors' => $errors,
                'address' => $address,
                'city' => $city,
                'postal_code' => $postalCode,
                'phone' => $phone,
                'notes' => $notes,
                'cart' => $cart
            ];
        }

        // Display checkout form
        $cart = $this->cartController->getCartWithProducts();

        if (empty($cart['items'])) {
            redirect('/views/user/cart.php');
        }

        return [
            'errors' => [],
            'address' => '',
            'city' => '',
            'postal_code' => '',
            'phone' => '',
            'notes' => '',
            'cart' => $cart
        ];
    }

    // Get user's orders
    public function getUserOrders() {
        if (!isLoggedIn()) {
            redirect('/views/auth/login.php');
        }

        $orders = $this->orderModel->getUserOrders($_SESSION['user_id']);

        // Get order items for each order
        foreach ($orders as &$order) {
            $order['items'] = $this->orderModel->getOrderItems($order['id']);
            $order['delivery'] = $this->deliveryModel->getDeliveryByOrderId($order['id']);
        }

        return [
            'orders' => $orders
        ];
    }

    // Get order details
    public function getOrderDetails($orderId) {
        if (!isLoggedIn()) {
            redirect('/views/auth/login.php');
        }

        $order = $this->orderModel->getOrderById($orderId);

        if (!$order || $order['user_id'] != $_SESSION['user_id']) {
            redirect('/views/user/orders.php');
        }

        $order['items'] = $this->orderModel->getOrderItems($orderId);
        $order['delivery'] = $this->deliveryModel->getDeliveryByOrderId($orderId);

        return [
            'order' => $order
        ];
    }
}
?>

