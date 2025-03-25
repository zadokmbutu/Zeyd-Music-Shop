<?php
// Start the session securely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    session_regenerate_id(true);
}

// Redirect if user is not logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = 'checkout.php';
    header('Location: login.php');
    exit();
}

// Redirect if cart is empty
if (empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit();
}

// Database connection (using PDO)
define('DB_HOST', 'localhost');
define('DB_NAME', 'ecommerce');
define('DB_USER', 'root');
define('DB_PASS', '');

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Get user details
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Function to get product details
function getProductDetails($pdo, $product_id) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    return $stmt->fetch();
}

// Calculate cart totals
$subtotal = 0;
$cart_items = [];
$shipping_fee = 5.99;

foreach ($_SESSION['cart'] as $product_id => $quantity) {
    $product = getProductDetails($pdo, $product_id);
    if ($product) {
        $item_subtotal = $product['price'] * $quantity;
        $subtotal += $item_subtotal;
        $cart_items[] = [
            'id' => $product_id,
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => $quantity,
            'subtotal' => $item_subtotal,
            'image' => $product['image'] ?? ''
        ];
    }
}

$total = $subtotal + $shipping_fee;

// Validate input function
function validateInput($data) {
    return htmlspecialchars(trim($data));
}

$errors = [];
$order_success = false;
$order_id = null;

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = validateInput($_POST['full_name']);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) ? $_POST['email'] : null;
    $address = validateInput($_POST['address']);
    $city = validateInput($_POST['city']);
    $state = validateInput($_POST['state']);
    $zip_code = preg_match('/^\d{5}(-\d{4})?$/', $_POST['zip_code']) ? $_POST['zip_code'] : null;
    $country = validateInput($_POST['country']);
    $payment_method = $_POST['payment_method'] ?? '';

    if (!$full_name) $errors[] = "Full name is required.";
    if (!$email) $errors[] = "Valid email is required.";
    if (!$address) $errors[] = "Address is required.";
    if (!$city) $errors[] = "City is required.";
    if (!$state) $errors[] = "State is required.";
    if (!$zip_code) $errors[] = "Valid ZIP code is required.";
    if (!$country) $errors[] = "Country is required.";
    if (!$payment_method) $errors[] = "Payment method is required.";

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();
            $order_date = date('Y-m-d H:i:s');
            $status = 'pending';

            // Insert order into database
            $order_stmt = $pdo->prepare("INSERT INTO orders (user_id, order_date, total_amount, shipping_fee, status, 
                                          full_name, email, address, city, state, zip_code, country, payment_method) 
                                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $order_stmt->execute([$user_id, $order_date, $total, $shipping_fee, $status, 
                                  $full_name, $email, $address, $city, $state, $zip_code, $country, $payment_method]);
            $order_id = $pdo->lastInsertId();

            // Insert order items
            $item_stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            foreach ($cart_items as $item) {
                $item_stmt->execute([$order_id, $item['id'], $item['quantity'], $item['price']]);
            }

            $pdo->commit();
            $_SESSION['cart'] = [];
            $order_success = true;
        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = "Order failed: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <?php if ($order_success): ?>
            <div class="alert alert-success">
                <h4>Order Placed Successfully!</h4>
                <p>Your order number is: <strong>#<?php echo $order_id; ?></strong></p>
                <p>A confirmation email has been sent.</p>
                <a href="index.php" class="btn btn-primary">Continue Shopping</a>
            </div>
        <?php else: ?>
            <h1>Checkout</h1>

            <?php if ($errors): ?>
                <div class="alert alert-danger">
                    <ul><?php foreach ($errors as $error) echo "<li>$error</li>"; ?></ul>
                </div>
            <?php endif; ?>

            <form method="post">
                <label>Full Name:</label>
                <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($user['name'] ?? '') ?>" required>
                
                <label>Email:</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>

                <label>Address:</label>
                <input type="text" name="address" class="form-control" required>

                <label>City:</label>
                <input type="text" name="city" class="form-control" required>

                <label>State:</label>
                <input type="text" name="state" class="form-control" required>

                <label>ZIP Code:</label>
                <input type="text" name="zip_code" class="form-control" required>

                <label>Country:</label>
                <input type="text" name="country" class="form-control" required>

                <label>Payment Method:</label>
                <select name="payment_method" class="form-control">
                    <option value="credit_card">Credit Card</option>
                    <option value="paypal">PayPal</option>
                </select>

                <button type="submit" class="btn btn-success mt-3">Place Order</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
