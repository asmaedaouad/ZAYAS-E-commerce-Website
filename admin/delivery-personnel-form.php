<?php
// Include configuration
require_once '../config/config.php';
require_once '../config/Database.php';
require_once './controllers/AdminDeliveryController.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('/views/auth/login.php');
}

// Set page title and CSS
$isEdit = isset($_GET['id']) && !empty($_GET['id']);
$pageTitle = $isEdit ? 'Edit Delivery Personnel' : 'Add Delivery Personnel';
$customCss = 'delivery.css';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Create delivery controller
$deliveryController = new AdminDeliveryController($db);

// Initialize variables
$personnel = [
    'id' => '',
    'first_name' => '',
    'last_name' => '',
    'email' => '',
    'phone' => '',
    'address' => '',
    'city' => '',
    'postal_code' => ''
];
$formErrors = [];
$successMessage = '';

// If editing, get personnel data
if ($isEdit) {
    $personnelId = (int)$_GET['id'];
    $personnel = $deliveryController->getDeliveryPersonnelById($personnelId);

    if (!$personnel) {
        redirect('/admin/delivery-personnel.php');
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $personnelData = [
        'first_name' => isset($_POST['first_name']) ? sanitize($_POST['first_name']) : '',
        'last_name' => isset($_POST['last_name']) ? sanitize($_POST['last_name']) : '',
        'email' => isset($_POST['email']) ? sanitize($_POST['email']) : '',
        'phone' => isset($_POST['phone']) ? sanitize($_POST['phone']) : '',
        'address' => isset($_POST['address']) ? sanitize($_POST['address']) : '',
        'city' => isset($_POST['city']) ? sanitize($_POST['city']) : '',
        'postal_code' => isset($_POST['postal_code']) ? sanitize($_POST['postal_code']) : '',
        'password' => isset($_POST['password']) ? $_POST['password'] : '',
        'confirm_password' => isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '',
        'is_delivery' => 1
    ];

    // Validate form data
    if (empty($personnelData['first_name'])) {
        $formErrors['first_name'] = 'First name is required';
    } elseif (!preg_match('/^[a-zA-ZÀ-ÿ\s\'\-]{2,50}$/', $personnelData['first_name'])) {
        $formErrors['first_name'] = 'First name must be 2-50 characters and contain only letters, spaces, hyphens, and apostrophes';
    }

    if (empty($personnelData['last_name'])) {
        $formErrors['last_name'] = 'Last name is required';
    } elseif (!preg_match('/^[a-zA-ZÀ-ÿ\s\'\-]{2,50}$/', $personnelData['last_name'])) {
        $formErrors['last_name'] = 'Last name must be 2-50 characters and contain only letters, spaces, hyphens, and apostrophes';
    }

    if (empty($personnelData['email'])) {
        $formErrors['email'] = 'Email is required';
    } elseif (!filter_var($personnelData['email'], FILTER_VALIDATE_EMAIL)) {
        $formErrors['email'] = 'Invalid email format';
    } elseif (!preg_match('/^[a-zA-Z][a-zA-Z0-9]*@[a-zA-Z]+\.[a-zA-Z]+$/', $personnelData['email'])) {
        $formErrors['email'] = 'Email must start with a letter, may contain numbers, and domain must contain only letters';
    } elseif ($deliveryController->emailExists($personnelData['email'], $isEdit ? $personnel['id'] : null)) {
        $formErrors['email'] = 'Email already exists';
    }

    if (!empty($personnelData['city']) && !preg_match('/^[a-zA-ZÀ-ÿ\s\'\-]{2,50}$/', $personnelData['city'])) {
        $formErrors['city'] = 'City must contain only alphabetic characters';
    }

    // Password validation for new personnel or when changing password
    if (!$isEdit || !empty($personnelData['password'])) {
        if (empty($personnelData['password'])) {
            $formErrors['password'] = 'Password is required';
        } elseif (strlen($personnelData['password']) < 6) {
            $formErrors['password'] = 'Password must be at least 6 characters';
        }

        if (empty($personnelData['confirm_password'])) {
            $formErrors['confirm_password'] = 'Please confirm password';
        } elseif ($personnelData['password'] !== $personnelData['confirm_password']) {
            $formErrors['confirm_password'] = 'Passwords do not match';
        }
    }

    // If no errors, create or update personnel
    if (empty($formErrors)) {
        if ($isEdit) {
            // Update existing personnel
            if ($deliveryController->updateDeliveryPersonnel($personnel['id'], $personnelData)) {
                $successMessage = 'Delivery personnel updated successfully';
                // Refresh personnel data
                $personnel = $deliveryController->getDeliveryPersonnelById($personnel['id']);
            } else {
                $formErrors['general'] = 'Failed to update delivery personnel';
            }
        } else {
            // Create new personnel
            $newPersonnelId = $deliveryController->createDeliveryPersonnel($personnelData);
            if ($newPersonnelId) {
                redirect('/admin/delivery-personnel.php');
            } else {
                $formErrors['general'] = 'Failed to create delivery personnel';
            }
        }
    }
}

// Include header
include_once './includes/header.php';
?>

<!-- Delivery Personnel Form Content -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-truck me-2"></i> <?php echo $isEdit ? 'Edit' : 'Add New'; ?> Delivery Personnel</span>
                <a href="<?php echo url('/admin/delivery-personnel.php'); ?>" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Delivery Personnel
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

                <form method="POST" action="<?php echo $_SERVER['PHP_SELF'] . ($isEdit ? '?id=' . $personnel['id'] : ''); ?>">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?php echo isset($formErrors['first_name']) ? 'is-invalid' : ''; ?>" id="first_name" name="first_name" value="<?php echo htmlspecialchars($personnel['first_name']); ?>" required>
                            <?php if (isset($formErrors['first_name'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo $formErrors['first_name']; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?php echo isset($formErrors['last_name']) ? 'is-invalid' : ''; ?>" id="last_name" name="last_name" value="<?php echo htmlspecialchars($personnel['last_name']); ?>" required>
                            <?php if (isset($formErrors['last_name'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo $formErrors['last_name']; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control <?php echo isset($formErrors['email']) ? 'is-invalid' : ''; ?>" id="email" name="email" value="<?php echo htmlspecialchars($personnel['email']); ?>" required>
                            <?php if (isset($formErrors['email'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo $formErrors['email']; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" class="form-control <?php echo isset($formErrors['phone']) ? 'is-invalid' : ''; ?>" id="phone" name="phone" value="<?php echo htmlspecialchars($personnel['phone']); ?>">
                            <?php if (isset($formErrors['phone'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo $formErrors['phone']; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" class="form-control <?php echo isset($formErrors['address']) ? 'is-invalid' : ''; ?>" id="address" name="address" value="<?php echo htmlspecialchars($personnel['address']); ?>">
                            <?php if (isset($formErrors['address'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo $formErrors['address']; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="city" class="form-label">City</label>
                            <input type="text" class="form-control <?php echo isset($formErrors['city']) ? 'is-invalid' : ''; ?>" id="city" name="city" value="<?php echo htmlspecialchars($personnel['city']); ?>">
                            <?php if (isset($formErrors['city'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo $formErrors['city']; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="postal_code" class="form-label">Postal Code</label>
                            <input type="text" class="form-control <?php echo isset($formErrors['postal_code']) ? 'is-invalid' : ''; ?>" id="postal_code" name="postal_code" value="<?php echo htmlspecialchars($personnel['postal_code']); ?>">
                            <?php if (isset($formErrors['postal_code'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo $formErrors['postal_code']; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label"><?php echo $isEdit ? 'New Password (leave blank to keep current)' : 'Password <span class="text-danger">*</span>'; ?></label>
                            <input type="password" class="form-control <?php echo isset($formErrors['password']) ? 'is-invalid' : ''; ?>" id="password" name="password" <?php echo $isEdit ? '' : 'required'; ?>>
                            <?php if (isset($formErrors['password'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo $formErrors['password']; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="confirm_password" class="form-label"><?php echo $isEdit ? 'Confirm New Password' : 'Confirm Password <span class="text-danger">*</span>'; ?></label>
                            <input type="password" class="form-control <?php echo isset($formErrors['confirm_password']) ? 'is-invalid' : ''; ?>" id="confirm_password" name="confirm_password" <?php echo $isEdit ? '' : 'required'; ?>>
                            <?php if (isset($formErrors['confirm_password'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo $formErrors['confirm_password']; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> <?php echo $isEdit ? 'Update' : 'Create'; ?> Delivery Personnel
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

