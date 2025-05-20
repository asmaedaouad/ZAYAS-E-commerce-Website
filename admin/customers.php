<?php
// Include configuration
require_once '../config/config.php';
require_once '../config/Database.php';
require_once './controllers/AdminCustomerController.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('/views/auth/login.php');
}

// Set page title
$pageTitle = 'Customers';
$customCss = 'customers.css';
$customJs = 'customers.js';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Create customer controller
$customerController = new AdminCustomerController($db);

// Get all customers without filters
$customers = $customerController->getCustomers();

// Include header
include_once './includes/header.php';

// Display messages if any
if (isset($_SESSION['admin_message'])):
    $messageType = $_SESSION['admin_message']['type'];
    $messageText = $_SESSION['admin_message']['text'];
?>
<div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
    <?php echo $messageText; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php
    // Clear the message after displaying
    unset($_SESSION['admin_message']);
endif;
?>

<!-- Customers Content -->

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-users me-2"></i> Customer List
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
                                <th>Joined</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($customers)): ?>
                                <tr>
                                    <td colspan="6" class="text-center">No customers found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($customers as $customer): ?>
                                    <tr>
                                        <td><?php echo $customer['id']; ?></td>
                                        <td><?php echo htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']); ?></td>
                                        <td><?php echo htmlspecialchars($customer['email']); ?></td>
                                        <td><?php echo !empty($customer['phone']) ? htmlspecialchars($customer['phone']) : 'N/A'; ?></td>
                                        <td><?php echo $customerController->formatDate($customer['created_at']); ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="<?php echo url('/admin/customer-details.php?id=' . $customer['id']); ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger delete-btn"
                                                    data-id="<?php echo $customer['id']; ?>"
                                                    data-name="<?php echo htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']); ?>">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </div>
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

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteConfirmModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="deleteConfirmText">Are you sure you want to delete this customer?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="<?php echo url('/admin/customer-delete.php'); ?>" method="get">
                    <input type="hidden" id="deleteCustomerId" name="id" value="">
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
