<?php
// Start session
session_start();

// Initialize cart
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Database connection
$servername = "localhost";
$username = "root"; // Update if needed
$password = ""; // Update if needed
$dbname = "ecommerce"; // Update if needed

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Function to get product details
function getProductDetails($conn, $product_id) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Handle Add to Cart
if (isset($_POST['add_to_cart'])) {
    $product_id = (int)$_POST['product_id'];
    $quantity = max(1, (int)$_POST['quantity']); // Ensure at least 1

    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }
    header("Location: cart.php");
    exit();
}

// Handle Update Cart
if (isset($_POST['update_cart'])) {
    foreach ($_POST['quantity'] as $product_id => $quantity) {
        $product_id = (int)$product_id;
        $quantity = max(0, (int)$quantity); // Prevent negative values

        if ($quantity > 0) {
            $_SESSION['cart'][$product_id] = $quantity;
        } else {
            unset($_SESSION['cart'][$product_id]); // Remove if 0
        }
    }
    header("Location: cart.php");
    exit();
}

// Handle Remove Item
if (isset($_GET['remove'])) {
    $product_id = (int)$_GET['remove'];
    unset($_SESSION['cart'][$product_id]);
    header("Location: cart.php");
    exit();
}

// Calculate total price
$total_price = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Music Shop - Shopping Cart</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background: url('music-background.jpg') no-repeat center center fixed;
            background-size: cover;
            color: white;
        }
        .cart-container {
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
            background: rgba(0, 0, 0, 0.8);
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.2);
        }
        .product-image {
            max-width: 100px;
            max-height: 100px;
            border-radius: 5px;
        }
        .quantity-input {
            width: 60px;
            text-align: center;
        }
        .btn-danger, .btn-secondary, .btn-info, .btn-success {
            border-radius: 5px;
        }
        @media (max-width: 600px) {
            .quantity-input {
                width: 50px;
            }
            table {
                font-size: 14px;
            }
        }
        h1, th {
            color: #f9d342;
        }
    </style>
</head>
<body>

    <div class="cart-container">
        <h1 class="mb-4 text-center">🎸 Your Music Cart 🎵</h1>

        <?php if (empty($_SESSION['cart'])): ?>
            <div class="alert alert-warning text-center">Your cart is empty. Add some music magic!</div>
            <div class="text-center">
                <a href="index.php" class="btn btn-primary">Continue Shopping</a>
            </div>
        <?php else: ?>
            
            <form method="post" action="cart.php">
                <table class="table table-dark table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Product</th>
                            <th>Price ($)</th>
                            <th>Quantity</th>
                            <th>Subtotal ($)</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($_SESSION['cart'] as $product_id => $quantity): 
                            $product = getProductDetails($conn, $product_id);
                            
                            if ($product) {
                                $subtotal = $product['price'] * $quantity;
                                $total_price += $subtotal;
                        ?>
                        <tr>
                            <td>
                                <?php if (!empty($product['image'])): ?>
                                    <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-image mr-2">
                                <?php endif; ?>
                                <?= htmlspecialchars($product['name']) ?>
                            </td>
                            <td>$<?= number_format($product['price'], 2) ?></td>
                            <td>
                                <input type="number" name="quantity[<?= $product_id ?>]" value="<?= $quantity ?>" min="0" class="form-control quantity-input">
                            </td>
                            <td>$<?= number_format($subtotal, 2) ?></td>
                            <td>
                                <a href="cart.php?remove=<?= $product_id ?>" class="btn btn-danger btn-sm">Remove</a>
                            </td>
                        </tr>
                        <?php 
                            }
                        endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-right"><strong>Total:</strong></td>
                            <td colspan="2"><strong>$<?= number_format($total_price, 2) ?></strong></td>
                        </tr>
                    </tfoot>
                </table>
                
                <div class="d-flex justify-content-between">
                    <a href="index.php" class="btn btn-secondary">🎶 Continue Shopping</a>
                    <button type="submit" name="update_cart" class="btn btn-info">🔄 Update Cart</button>
                    <a href="checkout.php" class="btn btn-success">💳 Checkout</a>
                </div>
            </form>
            
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
