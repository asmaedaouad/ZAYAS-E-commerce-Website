<?php
// Include configuration
require_once '../../config/config.php';
require_once '../../config/Database.php';
require_once '../../controllers/CartController.php';

// Allow non-logged in users to remove from cart (will be stored in session)
// No redirect needed here

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Debug mode
$debug = isset($_GET['debug']) && $_GET['debug'] == 1;

// Get product ID from either POST or GET
$productId = 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
} else if (isset($_GET['product_id'])) {
    $productId = (int)$_GET['product_id'];
}

// Get user ID
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

if ($debug) {
    echo '<pre>';
    echo "Cart Removal Debug\n";
    echo "Method: " . $_SERVER['REQUEST_METHOD'] . "\n";
    echo "Product ID: $productId\n";
    echo "User ID: $userId\n";
    echo '</pre>';
}

// Direct database removal (more reliable than going through the controller)
if ($productId > 0) {
    try {
        // First check if the item exists
        if ($debug) {
            $checkQuery = "SELECT * FROM cart WHERE user_id = :user_id AND product_id = :product_id";
            $checkStmt = $db->prepare($checkQuery);
            $checkStmt->bindParam(':user_id', $userId);
            $checkStmt->bindParam(':product_id', $productId);
            $checkStmt->execute();

            echo '<pre>';
            echo "Item exists in cart: " . ($checkStmt->rowCount() > 0 ? 'Yes' : 'No') . "\n";
            if ($checkStmt->rowCount() > 0) {
                $item = $checkStmt->fetch(PDO::FETCH_ASSOC);
                print_r($item);
            }
            echo '</pre>';
        }

        // Direct database query to remove the item
        $query = "DELETE FROM cart WHERE user_id = :user_id AND product_id = :product_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':product_id', $productId);
        $result = $stmt->execute();

        if ($debug) {
            echo '<pre>';
            echo "Direct query result: " . ($result ? 'Success' : 'Failed') . "\n";
            echo "Rows affected: " . $stmt->rowCount() . "\n";

            // Show all cart items after removal
            $allItemsQuery = "SELECT * FROM cart WHERE user_id = :user_id";
            $allItemsStmt = $db->prepare($allItemsQuery);
            $allItemsStmt->bindParam(':user_id', $userId);
            $allItemsStmt->execute();

            echo "All cart items after removal:\n";
            $remainingItems = [];
            while ($row = $allItemsStmt->fetch(PDO::FETCH_ASSOC)) {
                $remainingItems[] = $row;
                print_r($row);
            }
            echo "Total items remaining: " . count($remainingItems) . "\n";
            echo '</pre>';

            echo '<p><a href="' . url('/views/user/account.php#cart') . '">Return to cart</a></p>';
            exit;
        }
    } catch (Exception $e) {
        if ($debug) {
            echo '<pre>';
            echo "Error: " . $e->getMessage() . "\n";
            echo '</pre>';
            exit;
        }
    }
} else if ($debug) {
    echo '<pre>';
    echo "Error: No product ID provided\n";
    echo '</pre>';
    exit;
}

// Redirect back to account page cart tab
redirect('/views/user/account.php#cart');
?>
