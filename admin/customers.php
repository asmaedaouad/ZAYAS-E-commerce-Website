<?php
// Include configuration
require_once '../config/config.php';
require_once '../config/Database.php';
require_once './controllers/AdminCustomerController.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('/admin/login.php');
}

// Set page title
$pageTitle = 'Customers';
$customCss = 'customers.css';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Create customer controller
$customerController = new AdminCustomerController($db);

// Process filters
$filters = [];

if (isset($_GET['is_delivery']) && $_GET['is_delivery'] !== '') {
    $filters['is_delivery'] = $_GET['is_delivery'];
}

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $filters['search'] = $_GET['search'];
}

// Get customers with filters
$customers = $customerController->getCustomers($filters);

// Include header
include_once './includes/header.php';
?>

<!-- Customers Content -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-filter me-2"></i> Filter Customers
            </div>
            <div class="card-body">
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label for="is_delivery" class="form-label">Customer Type</label>
                        <select class="form-select" id="is_delivery" name="is_delivery">
                            <option value="">All Types</option>
                            <option value="0" <?php echo (isset($filters['is_delivery']) && $filters['is_delivery'] === '0') ? 'selected' : ''; ?>>Regular Customers</option>
                            <option value="1" <?php echo (isset($filters['is_delivery']) && $filters['is_delivery'] === '1') ? 'selected' : ''; ?>>Delivery Personnel</option>
                        </select>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" id="search" name="search" placeholder="Name or email..." value="<?php echo isset($filters['search']) ? htmlspecialchars($filters['search']) : ''; ?>">
                    </div>
                    
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">Apply Filters</button>
                        <a href="<?php echo url('/admin/customers.php'); ?>" class="btn btn-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

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
                                <th>Type</th>
                                <th>Phone</th>
                                <th>Joined</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($customers)): ?>
                                <tr>
                                    <td colspan="7" class="text-center">No customers found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($customers as $customer): ?>
                                    <tr>
                                        <td><?php echo $customer['id']; ?></td>
                                        <td><?php echo htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']); ?></td>
                                        <td><?php echo htmlspecialchars($customer['email']); ?></td>
                                        <td>
                                            <span class="badge <?php echo $customerController->getCustomerTypeBadgeClass($customer['is_delivery']); ?>">
                                                <?php echo $customerController->getCustomerTypeLabel($customer['is_delivery']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo !empty($customer['phone']) ? htmlspecialchars($customer['phone']) : 'N/A'; ?></td>
                                        <td><?php echo $customerController->formatDate($customer['created_at']); ?></td>
                                        <td>
                                            <a href="<?php echo url('/admin/customer-details.php?id=' . $customer['id']); ?>" class="btn btn-sm btn-outline-primary">
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
