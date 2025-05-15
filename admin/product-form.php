<?php
// Include configuration
require_once '../config/config.php';
require_once '../config/Database.php';
require_once './controllers/AdminProductController.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('/views/auth/login.php');
}

// Set page title and custom CSS
$pageTitle = 'Product Form';
$customCss = 'product-form.css';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Create product controller
$productController = new AdminProductController($db);

// Get product types for dropdown
$productTypes = $productController->getProductTypes();

// Initialize variables
$product = [
    'id' => '',
    'name' => '',
    'type' => '',
    'description' => '',
    'price' => '',
    'old_price' => '',
    'image_path' => '',
    'is_new' => 0,
    'quantity' => ''
];
$isEdit = false;
$formErrors = [];
$successMessage = '';

// Check if it's an edit operation
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $productId = (int)$_GET['id'];
    $product = $productController->getProductById($productId);
    
    if (!$product) {
        redirect('/admin/products.php');
    }
    
    $isEdit = true;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate form data
    $name = trim($_POST['name']);
    $type = trim($_POST['type']);
    $description = trim($_POST['description']);
    $price = (float)$_POST['price'];
    $oldPrice = !empty($_POST['old_price']) ? (float)$_POST['old_price'] : null;
    $isNew = isset($_POST['is_new']) ? 1 : 0;
    $quantity = (int)$_POST['quantity'];
    
    // Validate required fields
    if (empty($name)) {
        $formErrors['name'] = 'Product name is required';
    }
    
    if (empty($type)) {
        $formErrors['type'] = 'Product type is required';
    }
    
    if (empty($description)) {
        $formErrors['description'] = 'Product description is required';
    }
    
    if ($price <= 0) {
        $formErrors['price'] = 'Price must be greater than zero';
    }
    
    if ($quantity < 0) {
        $formErrors['quantity'] = 'Quantity cannot be negative';
    }
    
    // Handle image upload
    $imagePath = $isEdit ? $product['image_path'] : '';
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = $_FILES['image']['type'];
        
        if (!in_array($fileType, $allowedTypes)) {
            $formErrors['image'] = 'Only JPG, PNG, and GIF images are allowed';
        } else {
            // Generate unique filename based on product ID or timestamp
            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename = $isEdit ? $product['id'] : time();
            $filename .= '.' . $extension;
            
            $uploadDir = '../public/images/';
            $uploadPath = $uploadDir . $filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                $imagePath = $filename;
            } else {
                $formErrors['image'] = 'Failed to upload image';
            }
        }
    } elseif (!$isEdit && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        // Handle upload errors
        $uploadErrors = [
            UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
            UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form',
            UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload'
        ];
        
        $errorCode = $_FILES['image']['error'];
        $formErrors['image'] = isset($uploadErrors[$errorCode]) ? $uploadErrors[$errorCode] : 'Unknown upload error';
    } elseif (!$isEdit && $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
        $formErrors['image'] = 'Product image is required';
    }
    
    // If no errors, save the product
    if (empty($formErrors)) {
        $productData = [
            'name' => $name,
            'type' => $type,
            'description' => $description,
            'price' => $price,
            'old_price' => $oldPrice,
            'image_path' => $imagePath,
            'is_new' => $isNew,
            'quantity' => $quantity
        ];
        
        if ($isEdit) {
            // Update existing product
            if ($productController->updateProduct($product['id'], $productData)) {
                $successMessage = 'Product updated successfully';
                // Refresh product data
                $product = $productController->getProductById($product['id']);
            } else {
                $formErrors['general'] = 'Failed to update product';
            }
        } else {
            // Create new product
            $newProductId = $productController->createProduct($productData);
            if ($newProductId) {
                redirect('/admin/products.php');
            } else {
                $formErrors['general'] = 'Failed to create product';
            }
        }
    }
}

// Include header
include_once './includes/header.php';
?>

<!-- Product Form Content -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-<?php echo $isEdit ? 'edit' : 'plus'; ?> me-2"></i> <?php echo $isEdit ? 'Edit' : 'Add New'; ?> Product</span>
                <a href="<?php echo url('/admin/products.php'); ?>" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Products
                </a>
            </div>
            <div class="card-body">
                <?php if (!empty($successMessage)): ?>
                    <div class="alert alert-success">
                        <?php echo $successMessage; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($formErrors['general'])): ?>
                    <div class="alert alert-danger">
                        <?php echo $formErrors['general']; ?>
                    </div>
                <?php endif; ?>
                
                <form action="<?php echo $_SERVER['PHP_SELF'] . ($isEdit ? '?id=' . $product['id'] : ''); ?>" method="POST" enctype="multipart/form-data" class="product-form">
                    <div class="row">
                        <div class="col-md-8">
                            <!-- Basic Information -->
                            <div class="mb-3">
                                <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?php echo isset($formErrors['name']) ? 'is-invalid' : ''; ?>" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                                <?php if (isset($formErrors['name'])): ?>
                                    <div class="invalid-feedback"><?php echo $formErrors['name']; ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="type" class="form-label">Product Type <span class="text-danger">*</span></label>
                                        <select class="form-select <?php echo isset($formErrors['type']) ? 'is-invalid' : ''; ?>" id="type" name="type" required>
                                            <option value="">Select Type</option>
                                            <?php foreach ($productTypes as $type): ?>
                                                <option value="<?php echo $type; ?>" <?php echo ($product['type'] === $type) ? 'selected' : ''; ?>>
                                                    <?php echo ucfirst($type); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <?php if (isset($formErrors['type'])): ?>
                                            <div class="invalid-feedback"><?php echo $formErrors['type']; ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control <?php echo isset($formErrors['quantity']) ? 'is-invalid' : ''; ?>" id="quantity" name="quantity" value="<?php echo htmlspecialchars($product['quantity']); ?>" min="0" required>
                                        <?php if (isset($formErrors['quantity'])): ?>
                                            <div class="invalid-feedback"><?php echo $formErrors['quantity']; ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" step="0.01" class="form-control <?php echo isset($formErrors['price']) ? 'is-invalid' : ''; ?>" id="price" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" min="0.01" required>
                                            <?php if (isset($formErrors['price'])): ?>
                                                <div class="invalid-feedback"><?php echo $formErrors['price']; ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="old_price" class="form-label">Old Price (Optional)</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" step="0.01" class="form-control" id="old_price" name="old_price" value="<?php echo htmlspecialchars($product['old_price']); ?>" min="0">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                                <textarea class="form-control <?php echo isset($formErrors['description']) ? 'is-invalid' : ''; ?>" id="description" name="description" rows="5" required><?php echo htmlspecialchars($product['description']); ?></textarea>
                                <?php if (isset($formErrors['description'])): ?>
                                    <div class="invalid-feedback"><?php echo $formErrors['description']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <!-- Product Image -->
                            <div class="mb-3">
                                <label class="form-label">Product Image <span class="text-danger"><?php echo $isEdit ? '' : '*'; ?></span></label>
                                
                                <?php if ($isEdit && !empty($product['image_path'])): ?>
                                    <div class="current-image mb-3">
                                        <img src="<?php echo url('/public/images/' . $product['image_path']); ?>" alt="Current Product Image" class="img-thumbnail">
                                        <p class="text-muted small mt-2">Current image: <?php echo $product['image_path']; ?></p>
                                    </div>
                                <?php endif; ?>
                                
                                <input type="file" class="form-control <?php echo isset($formErrors['image']) ? 'is-invalid' : ''; ?>" id="image" name="image" accept="image/*" <?php echo $isEdit ? '' : 'required'; ?>>
                                <div class="form-text">Recommended size: 800x800 pixels. Max file size: 2MB.</div>
                                <?php if (isset($formErrors['image'])): ?>
                                    <div class="invalid-feedback"><?php echo $formErrors['image']; ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Product Status -->
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_new" name="is_new" value="1" <?php echo $product['is_new'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="is_new">
                                        Mark as New Arrival
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> <?php echo $isEdit ? 'Update' : 'Create'; ?> Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include_once './includes/footer.php';
?>

