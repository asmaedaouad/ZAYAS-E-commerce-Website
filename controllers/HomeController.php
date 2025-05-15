<?php
require_once __DIR__ . '/../models/ProductModel.php';

class HomeController {
    private $productModel;

    public function __construct($db) {
        $this->productModel = new ProductModel($db);
    }

    // Display home page
    public function index() {
        // Get new arrivals
        $newArrivals = $this->productModel->getNewArrivals(4);

        // Get featured products (just using regular products for simplicity)
        $featuredProducts = $this->productModel->getProducts(4);

        return [
            'new_arrivals' => $newArrivals,
            'featured_products' => $featuredProducts
        ];
    }

    // Display shop page
    public function shop() {
        // Get filter parameters
        $type = isset($_GET['type']) ? sanitize($_GET['type']) : null;
        $search = isset($_GET['search']) ? sanitize($_GET['search']) : null;

        // Get products based on filters
        if ($search) {
            $products = $this->productModel->searchProducts($search);
            $title = 'Search Results for "' . $search . '"';
        } elseif ($type) {
            $products = $this->productModel->getProducts(null, $type);
            $title = ucfirst($type) . 's';
        } else {
            $products = $this->productModel->getProducts();
            $title = 'All Products';
        }

        return [
            'products' => $products,
            'title' => $title,
            'type' => $type,
            'search' => $search
        ];
    }

    // Display product details
    public function productDetails($id) {
        // Get product
        $product = $this->productModel->getProductById($id);

        if (!$product) {
            // Product not found
            return [
                'error' => 'Product not found'
            ];
        }

        // Get related products (same type)
        $relatedProducts = $this->productModel->getProducts(4, $product['type']);

        return [
            'product' => $product,
            'related_products' => $relatedProducts
        ];
    }

    // Display about page
    public function about() {
        return [];
    }

    // Display contact page
    public function contact() {
        // Load contact settings from configuration file
        $contactSettingsFile = __DIR__ . '/../config/contact_settings.php';
        $contactSettings = file_exists($contactSettingsFile) ? include $contactSettingsFile : [];

        return [
            'contact_settings' => $contactSettings
        ];
    }
}
?>
