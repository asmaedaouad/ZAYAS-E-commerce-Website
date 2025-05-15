<?php
// Include configuration
require_once '../config/config.php';
require_once '../config/Database.php';
require_once './controllers/AdminProductController.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('/views/auth/login.php');
}

// Set page title
$pageTitle = 'Products';
$customCss = 'products.css';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Create product controller
$productController = new AdminProductController($db);

// Get all products
$products = $productController->getProducts();

// Include header
include_once './includes/header.php';
?>

<!-- Products Content -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-box me-2"></i> Products Management</span>
                <a href="<?php echo url('/admin/product-form.php'); ?>" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus me-1"></i> Add New Product
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-list me-2"></i> All Products
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($products)): ?>
                                <tr>
                                    <td colspan="8" class="text-center">No products found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($products as $product): ?>
                                    <?php $stockStatus = $productController->getStockStatus($product['quantity']); ?>
                                    <tr>
                                        <td><?php echo $product['id']; ?></td>
                                        <td>
                                            <img src="<?php echo url('/public/images/' . $product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-thumbnail">
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($product['name']); ?>
                                            <?php if ($product['is_new']): ?>
                                                <span class="badge bg-success ms-1">New</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo ucfirst($product['type']); ?></td>
                                        <td>
                                            <?php echo $productController->formatCurrency($product['price']); ?>
                                            <?php if ($product['old_price']): ?>
                                                <small class="text-muted text-decoration-line-through">
                                                    <?php echo $productController->formatCurrency($product['old_price']); ?>
                                                </small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $product['quantity']; ?></td>
                                        <td>
                                            <span class="<?php echo $stockStatus['class']; ?>">
                                                <?php echo $stockStatus['status']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="<?php echo url('/admin/product-form.php?id=' . $product['id']); ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="javascript:void(0);" onclick="confirmDelete(<?php echo $product['id']; ?>)" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
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



<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this product? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="#" id="confirmDeleteBtn" class="btn btn-danger">Delete</a>
            </div>
        </div>
    </div>
</div>

<!-- Display admin messages if any -->
<?php if (isset($_SESSION['admin_message'])): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Create alert element
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-<?php echo $_SESSION['admin_message']['type']; ?> alert-dismissible fade show';
        alertDiv.setAttribute('role', 'alert');
        alertDiv.innerHTML = `
            <?php echo $_SESSION['admin_message']['text']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;

        // Insert alert before the products table
        const tableCard = document.querySelector('.table-responsive').closest('.card');
        tableCard.insertBefore(alertDiv, tableCard.firstChild);

        // Auto dismiss after 5 seconds
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alertDiv);
            bsAlert.close();
        }, 5000);
    });
</script>
<?php
    // Clear the message after displaying
    unset($_SESSION['admin_message']);
endif;
?>

<script>
    // Delete confirmation
    function confirmDelete(productId) {
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        document.getElementById('confirmDeleteBtn').href = '<?php echo url('/admin/product-delete.php?id='); ?>' + productId;
        modal.show();
    }
</script>

<?php
// Include footer
include_once './includes/footer.php';
?>

