<?php
// Include configuration
require_once '../config/config.php';
require_once '../config/Database.php';
require_once './controllers/AdminCustomerController.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('/views/auth/login.php');
}

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect('/admin/customers.php');
}

// Set page title
$pageTitle = 'Customer Details';
$customCss = 'customers.css';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Create customer controller
$customerController = new AdminCustomerController($db);

// Get customer ID
$customerId = (int)$_GET['id'];

// Get customer details
$customer = $customerController->getCustomerById($customerId);

// If customer not found, redirect
if (!$customer) {
    redirect('/admin/customers.php');
}

// Get customer orders
$customerOrders = $customerController->getCustomerOrders($customerId);

// Include header
include_once './includes/header.php';
?>

<!-- Customer Details Content -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-user me-2"></i> Customer Details</span>
                <a href="<?php echo url('/admin/customers.php'); ?>" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Customers
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Customer Information -->
    <div class="col-md-4 mb-4">
        <div class="card customer-info-card">
            <div class="card-header">
                <i class="fas fa-user-circle me-2"></i> Customer Information
            </div>
            <div class="card-body">
                <h4 class="customer-name"><?php echo htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']); ?></h4>
                <p class="customer-id">ID: <?php echo $customer['id']; ?></p>

                <hr>

                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="info-content">
                        <h5>Email:</h5>
                        <p><?php echo htmlspecialchars($customer['email']); ?></p>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div class="info-content">
                        <h5>Phone:</h5>
                        <p><?php echo !empty($customer['phone']) ? htmlspecialchars($customer['phone']) : 'N/A'; ?></p>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="info-content">
                        <h5>Address:</h5>
                        <p>N/A</p>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="info-content">
                        <h5>Joined:</h5>
                        <p><?php echo $customerController->formatDate($customer['created_at']); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Orders -->
    <div class="col-md-8 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-shopping-bag me-2"></i> Customer Orders
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($customerOrders)): ?>
                                <tr>
                                    <td colspan="5" class="text-center">No orders found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($customerOrders as $order): ?>
                                    <tr>
                                        <td>#<?php echo $order['id']; ?></td>
                                        <td><?php echo $customerController->formatDate($order['created_at']); ?></td>
                                        <td><?php echo $customerController->formatCurrency($order['total_amount']); ?></td>
                                        <td>
                                            <span class="badge <?php echo $customerController->getOrderStatusBadgeClass($order['status']); ?>">
                                                <?php echo $customerController->formatStatus($order['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="<?php echo url('/admin/order-details.php?id=' . $order['id']); ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include_once './includes/footer.php';
?>

