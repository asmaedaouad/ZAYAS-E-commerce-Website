<?php
// Include configuration
require_once '../config/config.php';
require_once '../config/Database.php';
require_once './controllers/AdminProductController.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('/admin/login.php');
}

// Set page title
$pageTitle = 'Products';
$customCss = 'products.css';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Create product controller
$productController = new AdminProductController($db);

// Get product types for filter
$productTypes = $productController->getProductTypes();

// Process filters
$filters = [];

if (isset($_GET['type']) && !empty($_GET['type'])) {
    $filters['type'] = $_GET['type'];
}

if (isset($_GET['status']) && $_GET['status'] !== '') {
    $filters['status'] = $_GET['status'];
}

if (isset($_GET['is_new']) && $_GET['is_new'] !== '') {
    $filters['is_new'] = $_GET['is_new'];
}

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $filters['search'] = $_GET['search'];
}

// Get products with filters
$products = $productController->getProducts($filters);

// Include header
include_once './includes/header.php';
?>

<!-- Products Content -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-filter me-2"></i> Filter Products</span>
                <a href="<?php echo url('/admin/product-form.php'); ?>" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus me-1"></i> Add New Product
                </a>
            </div>
            <div class="card-body">
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label for="type" class="form-label">Product Type</label>
                        <select class="form-select" id="type" name="type">
                            <option value="">All Types</option>
                            <?php foreach ($productTypes as $type): ?>
                                <option value="<?php echo $type; ?>" <?php echo (isset($filters['type']) && $filters['type'] === $type) ? 'selected' : ''; ?>>
                                    <?php echo ucfirst($type); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="status" class="form-label">Stock Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">All Status</option>
                            <option value="in_stock" <?php echo (isset($filters['status']) && $filters['status'] === 'in_stock') ? 'selected' : ''; ?>>In Stock</option>
                            <option value="out_of_stock" <?php echo (isset($filters['status']) && $filters['status'] === 'out_of_stock') ? 'selected' : ''; ?>>Out of Stock</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="is_new" class="form-label">New Arrival</label>
                        <select class="form-select" id="is_new" name="is_new">
                            <option value="">All</option>
                            <option value="1" <?php echo (isset($filters['is_new']) && $filters['is_new'] === '1') ? 'selected' : ''; ?>>New Arrivals</option>
                            <option value="0" <?php echo (isset($filters['is_new']) && $filters['is_new'] === '0') ? 'selected' : ''; ?>>Regular Products</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" id="search" name="search" placeholder="Search products..." value="<?php echo isset($filters['search']) ? htmlspecialchars($filters['search']) : ''; ?>">
                    </div>
                    
                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                        <a href="<?php echo url('/admin/products.php'); ?>" class="btn btn-secondary">Reset</a>
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
                <i class="fas fa-box me-2"></i> Product List
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
                                            <img src="<?php echo url('/public/images/products/' . $product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-thumbnail">
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
