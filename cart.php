<?php
session_start();

// Simulated product list (replace with actual database query)
$products = [
    1 => ["name" => "Acoustic Guitar", "price" => 25800],
    2 => ["name" => "Electric Guitar", "price" => 45250],
    3 => ["name" => "Digital Piano", "price" => 65000],
    4 => ["name" => "Drum Set", "price" => 90300],
    5 => ["name" => "Violin", "price" => 19400],
    6 => ["name" => "Guitar Strings", "price" => 1290],
    7 => ["name" => "Drum Sticks", "price" => 1940],
    8 => ["name" => "Microphone", "price" => 11640],
    9 => ["name" => "Classical Music Album", "price" => 1680],
    10 => ["name" => "Rock Music Album", "price" => 2070],
];

// Initialize cart if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Calculate the total price of the cart
$total_price = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zeyd Music Shop - Cart</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
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
            background: rgba(0, 0, 0, 0.85);
            border-radius: 10px;
        }
        .product-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 5px;
        }
        .quantity-input {
            width: 60px;
            text-align: center;
        }
        .btn-danger, .btn-primary, .btn-info, .btn-success {
            border-radius: 5px;
        }
        h1, th {
            color: #ff6600;
        }
    </style>
</head>
<body>

<div class="cart-container">
    <h1 class="text-center">🛒 Your Shopping Cart</h1>

    <?php if (empty($_SESSION['cart'])): ?>
        <div class="alert alert-warning text-center">Your cart is empty. Start shopping now!</div>
        <div class="text-center">
            <a href="index.php" class="btn btn-primary">Continue Shopping</a>
        </div>
    <?php else: ?>
        <form method="post" action="cart.php">
            <table class="table table-dark table-bordered">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price (ksh)</th>
                        <th>Quantity</th>
                        <th>Subtotal (ksh)</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['cart'] as $product_id => $cart_item): 
                        $product = $products[$product_id] ?? null; // Get product data from the products array
                        if ($product) {
                            $subtotal = $product['price'] * $cart_item['quantity'];
                            $total_price += $subtotal;
                    ?>
                    <tr>
                        <td>
                            <?php if (!empty($product['image'])): ?>
                                <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-image">
                            <?php endif; ?>
                            <?= htmlspecialchars($product['name']) ?>
                        </td>
                        <td>ksh<?= number_format($product['price'], 2) ?></td>
                        <td>
                            <input type="number" name="quantity[<?= $product_id ?>]" value="<?= $cart_item['quantity'] ?>" min="1" class="form-control quantity-input">
                        </td>
                        <td>ksh<?= number_format($subtotal, 2) ?></td>
                        <td>
                            <a href="cart.php?remove=<?= $product_id ?>" class="btn btn-danger btn-sm">Remove</a>
                        </td>
                    </tr>
                    <?php } endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end"><strong>Total:</strong></td>
                        <td colspan="2"><strong>ksh<?= number_format($total_price, 2) ?></strong></td>
                    </tr>
                </tfoot>
            </table>
            <div class="d-flex justify-content-between">
                <a href="things.php" class="btn btn-secondary">🎶 Continue Shopping</a>
                <button type="submit" name="update_cart" class="btn btn-info">🔄 Update Cart</button>
                <a href="checkout.php" class="btn btn-success">💳 Checkout</a>
                <a href="cart.php?clear_cart=true" class="btn btn-warning">🗑 Clear Cart</a>
            </div>
        </form>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Handle Remove Item
if (isset($_GET['remove'])) {
    $product_id = (int)$_GET['remove'];
    unset($_SESSION['cart'][$product_id]);
    header("Location: cart.php");
    exit();
}

// Handle Clear Cart
if (isset($_GET['clear_cart'])) {
    $_SESSION['cart'] = [];
    header("Location: cart.php");
    exit();
}

?>
