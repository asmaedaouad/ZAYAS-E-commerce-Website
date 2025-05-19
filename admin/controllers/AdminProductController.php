<?php
require_once __DIR__ . '/../models/AdminProductModel.php';

class AdminProductController {
    private $productModel;

    public function __construct($db) {
        $this->productModel = new AdminProductModel($db);
    }

    // Get products with optional filtering
    public function getProducts($filters = []) {
        return $this->productModel->getProducts($filters);
    }

    // Get product by ID
    public function getProductById($id) {
        return $this->productModel->getProductById($id);
    }

    // Get product types
    public function getProductTypes() {
        return $this->productModel->getProductTypes();
    }

    // Create product
    public function createProduct($data) {
        return $this->productModel->createProduct($data);
    }

    // Update product
    public function updateProduct($id, $data) {
        return $this->productModel->updateProduct($id, $data);
    }

    // Delete product
    public function deleteProduct($id) {
        return $this->productModel->deleteProduct($id);
    }

    // Format currency
    public function formatCurrency($amount) {
        return number_format($amount, 2) . 'DH';
    }

    // Format date
    public function formatDate($date) {
        return date('M d, Y', strtotime($date));
    }

    // Get stock status
    public function getStockStatus($quantity) {
        if ($quantity > 10) {
            return ['status' => 'In Stock', 'class' => 'text-success'];
        } elseif ($quantity > 0) {
            return ['status' => 'Low Stock', 'class' => 'text-warning'];
        } else {
            return ['status' => 'Out of Stock', 'class' => 'text-danger'];
        }
    }
}
?>
