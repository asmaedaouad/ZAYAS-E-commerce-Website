<?php
// Include configuration
require_once '../config/config.php';
require_once '../config/Database.php';
require_once './controllers/AdminDeliveryController.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('/views/auth/login.php');
}

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect('/admin/delivery-personnel.php');
}

// Set page title
$pageTitle = 'Delivery Details';
$customCss = 'delivery.css';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Create delivery controller
$deliveryController = new AdminDeliveryController($db);

// Get delivery ID
$deliveryId = (int)$_GET['id'];

// Get delivery details
$delivery = $deliveryController->getDeliveryById($deliveryId);

// If delivery not found, redirect
if (!$delivery) {
    redirect('/admin/delivery-personnel.php');
}

// No status update handling needed

// Get delivery personnel
$deliveryPersonnel = $deliveryController->getDeliveryPersonnel();

// Include header
include_once './includes/header.php';
?>

<!-- Delivery Details Content -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-truck me-2"></i> Delivery Details</span>
                <div>
                    <?php if ($delivery['personnel_id']): ?>
                        <a href="<?php echo url('/admin/delivery-personnel-details.php?id=' . $delivery['personnel_id']); ?>" class="btn btn-sm btn-outline-primary me-2">
                            <i class="fas fa-user me-1"></i> View Delivery Person
                        </a>
                    <?php endif; ?>
                    <a href="<?php echo url('/admin/delivery-personnel.php'); ?>" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Delivery Personnel
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php
        echo htmlspecialchars($_SESSION['success_message']);
        unset($_SESSION['success_message']);
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php
        echo htmlspecialchars($_SESSION['error_message']);
        unset($_SESSION['error_message']);
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="row">
    <!-- Delivery Information -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-info-circle me-2"></i> Delivery Information
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Delivery ID:</strong>
                        <p class="mb-0"><?php echo $delivery['id']; ?></p>
                    </div>
                    <div class="col-md-6">
                        <strong>Order ID:</strong>
                        <p class="mb-0">#<?php echo $delivery['order_id']; ?></p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Status:</strong>
                        <p class="mb-0">
                            <span class="badge <?php echo $deliveryController->getStatusBadgeClass($delivery['delivery_status']); ?>">
                                <?php echo $deliveryController->formatStatus($delivery['delivery_status']); ?>
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <strong>Order Date:</strong>
                        <p class="mb-0"><?php echo $deliveryController->formatDate($delivery['order_date']); ?></p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-12">
                        <strong>Delivery Address:</strong>
                        <p class="mb-0">
                            <?php echo !empty($delivery['address']) ? htmlspecialchars($delivery['address']) : 'N/A'; ?><br>
                            <?php
                                $city = !empty($delivery['city']) ? htmlspecialchars($delivery['city']) : 'N/A';
                                $postalCode = !empty($delivery['postal_code']) ? htmlspecialchars($delivery['postal_code']) : '';
                                echo $city . (!empty($postalCode) ? ', ' . $postalCode : '');
                            ?>
                        </p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Phone:</strong>
                        <p class="mb-0"><?php echo !empty($delivery['phone']) ? htmlspecialchars($delivery['phone']) : 'N/A'; ?></p>
                    </div>
                    <div class="col-md-6">
                        <strong>Total Amount:</strong>
                        <p class="mb-0"><?php echo $deliveryController->formatCurrency($delivery['total_amount']); ?></p>
                    </div>
                </div>
                <?php if (!empty($delivery['delivery_notes'])): ?>
                <div class="row mb-3">
                    <div class="col-md-12">
                        <strong>Delivery Notes:</strong>
                        <p class="mb-0"><?php echo !empty($delivery['delivery_notes']) ? htmlspecialchars($delivery['delivery_notes']) : 'N/A'; ?></p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Customer Information -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-user me-2"></i> Customer Information
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-12">
                        <strong>Name:</strong>
                        <p class="mb-0"><?php
                            $firstName = !empty($delivery['first_name']) ? $delivery['first_name'] : '';
                            $lastName = !empty($delivery['last_name']) ? $delivery['last_name'] : '';
                            echo !empty($firstName) || !empty($lastName) ? htmlspecialchars($firstName . ' ' . $lastName) : 'N/A';
                        ?></p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-12">
                        <strong>Email:</strong>
                        <p class="mb-0"><?php echo !empty($delivery['email']) ? htmlspecialchars($delivery['email']) : 'N/A'; ?></p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-12">
                        <strong>Phone:</strong>
                        <p class="mb-0"><?php echo !empty($delivery['user_phone']) ? htmlspecialchars($delivery['user_phone']) : 'N/A'; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include_once './includes/footer.php';
?>

