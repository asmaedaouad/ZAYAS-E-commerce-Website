<?php
// Include configuration
require_once '../config/config.php';
require_once '../config/Database.php';
require_once './controllers/AdminProductController.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('/views/auth/login.php');
}

// Check if product ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect('/admin/products.php');
}

$productId = (int)$_GET['id'];

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Create product controller
$productController = new AdminProductController($db);

// Get product to check if it exists and to get the image path
$product = $productController->getProductById($productId);

if (!$product) {
    redirect('/admin/products.php');
}

// Delete product
if ($productController->deleteProduct($productId)) {
    // If product has an image, delete it from the server
    if (!empty($product['image_path'])) {
        $imagePath = '../public/images/' . $product['image_path'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }
    
    // Set success message in session
    $_SESSION['admin_message'] = [
        'type' => 'success',
        'text' => 'Product deleted successfully.'
    ];
} else {
    // Set error message in session
    $_SESSION['admin_message'] = [
        'type' => 'danger',
        'text' => 'Failed to delete product.'
    ];
}

// Redirect back to products page
redirect('/admin/products.php');
?>

