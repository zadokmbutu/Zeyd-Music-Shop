<?php
require_once 'config.php';
session_start();

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Function to validate CSRF token
function validate_csrf_token() {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        redirect('index.php', 'Invalid CSRF token', 'danger');
        exit();
    }
}

// Generate CSRF token if not set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Ensure an action is provided
$action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);
if (!$action) {
    redirect('index.php', 'Invalid request', 'danger');
}

// Handle different actions
try {
    switch ($action) {
        case 'add':
            validate_csrf_token();
            $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
            $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT, ["options" => ["min_range" => 1, "max_range" => 10]]);

            if (!$product_id || !$quantity) {
                redirect('index.php', 'Invalid product data', 'danger');
            }

            // Fetch product details
            $stmt = $pdo->prepare("SELECT product_id, name, price FROM products WHERE product_id = ?");
            $stmt->execute([$product_id]);
            $product = $stmt->fetch();

            if (!$product) {
                redirect('index.php', 'Product not found', 'danger');
            }

            // Update cart
            $_SESSION['cart'][$product_id] = [
                'id' => $product_id,
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => ($_SESSION['cart'][$product_id]['quantity'] ?? 0) + $quantity
            ];

            redirect('cart.php', 'Product added to cart', 'success');
            break;

        case 'update':
            validate_csrf_token();
            $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
            $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT, ["options" => ["min_range" => 0, "max_range" => 10]]);

            if (!$product_id || $quantity === false) {
                redirect('cart.php', 'Invalid product data', 'danger');
            }

            if ($quantity === 0) {
                unset($_SESSION['cart'][$product_id]);
                redirect('cart.php', 'Product removed from cart', 'info');
            } else {
                $_SESSION['cart'][$product_id]['quantity'] = $quantity;
                redirect('cart.php', 'Cart updated', 'success');
            }
            break;

        case 'remove':
            validate_csrf_token();
            $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
            if (!$product_id || !isset($_SESSION['cart'][$product_id])) {
                redirect('cart.php', 'Invalid product data', 'danger');
            }

            unset($_SESSION['cart'][$product_id]);
            redirect('cart.php', 'Product removed from cart', 'info');
            break;

        case 'clear':
            validate_csrf_token();
            $_SESSION['cart'] = [];
            redirect('cart.php', 'Cart cleared', 'info');
            break;

        default:
            redirect('index.php', 'Invalid action', 'danger');
    }
} catch (Exception $e) {
    redirect('index.php', 'An error occurred: ' . $e->getMessage(), 'danger');
}
?>
