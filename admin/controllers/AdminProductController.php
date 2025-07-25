<?php
require_once __DIR__ . '/../models/AdminProductModel.php';

class AdminProductController {
    private $productModel;

    public function __construct($db) {
        $this->productModel = new AdminProductModel($db);
    }

    
    public function getProducts($filters = []) {
        return $this->productModel->getProducts($filters);
    }

    
    public function getProductById($id) {
        return $this->productModel->getProductById($id);
    }

    
    public function getProductTypes() {
        return $this->productModel->getProductTypes();
    }

    
    public function createProduct($data) {
        return $this->productModel->createProduct($data);
    }

    
    public function updateProduct($id, $data) {
        return $this->productModel->updateProduct($id, $data);
    }

    
    public function deleteProduct($id) {
        return $this->productModel->deleteProduct($id);
    }

    
    public function formatCurrency($amount) {
        return number_format($amount, 2) . 'DH';
    }

    
    public function formatDate($date) {
        return date('M d, Y', strtotime($date));
    }

    
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
