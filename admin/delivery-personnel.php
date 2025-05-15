<?php
// Include configuration
require_once '../config/config.php';
require_once '../config/Database.php';
require_once './controllers/AdminDeliveryController.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('/views/auth/login.php');
}

// Set page title
$pageTitle = 'Delivery Personnel';
$customCss = 'delivery.css';
$customJs = 'delivery-personnel.js';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Create delivery controller
$deliveryController = new AdminDeliveryController($db);

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_personnel'])) {
    $personnelId = isset($_POST['personnel_id']) ? (int)$_POST['personnel_id'] : 0;

    if ($personnelId > 0) {
        if ($deliveryController->deleteDeliveryPersonnel($personnelId)) {
            $_SESSION['success_message'] = 'Delivery personnel deleted successfully';
        } else {
            $_SESSION['error_message'] = 'Failed to delete delivery personnel';
        }
    }

    // Redirect to avoid form resubmission
    redirect('/admin/delivery-personnel.php');
}

// Get all delivery personnel
$deliveryPersonnel = $deliveryController->getDeliveryPersonnel();

// Include header
include_once './includes/header.php';
?>

<!-- Delivery Personnel Content -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-truck me-2"></i> Delivery Personnel Management</span>
                <a href="<?php echo url('/admin/delivery-personnel-form.php'); ?>" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus me-1"></i> Add New Delivery Personnel
                </a>
            </div>
        </div>
    </div>
</div>

<?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php
        echo $_SESSION['success_message'];
        unset($_SESSION['success_message']);
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php
        echo $_SESSION['error_message'];
        unset($_SESSION['error_message']);
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-truck me-2"></i> Delivery Personnel List
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Active Orders</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($deliveryPersonnel)): ?>
                                <tr>
                                    <td colspan="7" class="text-center">No delivery personnel found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($deliveryPersonnel as $personnel): ?>
                                    <tr>
                                        <td><?php echo $personnel['id']; ?></td>
                                        <td><?php echo htmlspecialchars($personnel['first_name'] . ' ' . $personnel['last_name']); ?></td>
                                        <td><?php echo htmlspecialchars($personnel['email']); ?></td>
                                        <td><?php echo !empty($personnel['phone']) ? htmlspecialchars($personnel['phone']) : 'N/A'; ?></td>
                                        <td>
                                            <?php
                                            $activeOrders = $deliveryController->getActiveOrdersCount($personnel['id']);
                                            echo $activeOrders;
                                            ?>
                                        </td>
                                        <td><?php echo isset($personnel['created_at']) ? $deliveryController->formatDate($personnel['created_at']) : 'N/A'; ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="<?php echo url('/admin/delivery-personnel-details.php?id=' . $personnel['id']); ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <a href="<?php echo url('/admin/delivery-personnel-form.php?id=' . $personnel['id']); ?>" class="btn btn-sm btn-outline-secondary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger delete-btn"
                                                    data-id="<?php echo $personnel['id']; ?>"
                                                    data-name="<?php echo htmlspecialchars($personnel['first_name'] . ' ' . $personnel['last_name']); ?>"
                                                    data-active-orders="<?php echo $activeOrders; ?>">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </div>

                                            <!-- Delete Modal will be created dynamically -->
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

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteConfirmModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="deleteConfirmText">Are you sure you want to delete this delivery personnel?</p>
                <div id="warningContainer" class="alert alert-warning mt-3 d-none">
                    <strong>Warning:</strong> <span id="warningText"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" id="deleteForm">
                    <input type="hidden" name="personnel_id" id="deletePersonnelId" value="">
                    <button type="submit" name="delete_personnel" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include_once './includes/footer.php';
?>

