<?php
// Start the session to access user data
session_start();
include 'header.php' ;
include 'navbar.php' ;
 include 'database.php'; 

// Check if user is logged in, if not redirect to login page
if (!isset($_SESSION['user_id'])) {
    // Store the current page as intended destination after login
    $_SESSION['redirect_after_login'] = 'orders.php';
    header('Location: login.php');
    exit();
}

// Get user ID
$user_id = $_SESSION['user_id'];

// Database connection
$servername = "localhost";
$username = "root"; // Change to your database username
$password = ""; // Change to your database password
$dbname = "ecommerce"; // Change to your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get all orders for the current user, ordered by most recent first
$orders_query = "SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC";
$orders_stmt = $conn->prepare($orders_query);
$orders_stmt->bind_param("i", $user_id);
$orders_stmt->execute();
$orders_result = $orders_stmt->get_result();

// Function to get order items for a specific order
function getOrderItems($conn, $order_id) {
    $items_query = "SELECT oi.*, p.name, p.image FROM order_items oi 
                    JOIN products p ON oi.product_id = p.id 
                    WHERE oi.order_id = ?";
    $items_stmt = $conn->prepare($items_query);
    $items_stmt->bind_param("i", $order_id);
    $items_stmt->execute();
    return $items_stmt->get_result();
}

// Function to get order status class for styling
function getStatusClass($status) {
    switch (strtolower($status)) {
        case 'pending':
            return 'text-warning';
        case 'processing':
            return 'text-info';
        case 'shipped':
            return 'text-primary';
        case 'delivered':
            return 'text-success';
        case 'cancelled':
            return 'text-danger';
        default:
            return 'text-secondary';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <style>
        .orders-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }
        .order-card {
            margin-bottom: 30px;
            border: 1px solid #ddd;
            border-radius: 5px;
            overflow: hidden;
        }
        .order-header {
            background-color: #f8f9fa;
            padding: 15px;
            border-bottom: 1px solid #ddd;
        }
        .order-body {
            padding: 20px;
        }
        .product-image {
            max-width: 70px;
            max-height: 70px;
            object-fit: cover;
        }
        .order-details {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 15px;
        }
        .order-detail {
            flex: 1;
            min-width: 200px;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        .order-summary {
            background-color: #f8f9fa;
            padding: 10px 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .no-orders {
            text-align: center;
            padding: 50px 0;
        }
    </style>
</head>
<body>
    <div class="orders-container">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">My Orders</li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>My Orders</h1>
            <a href="index.php" class="btn btn-primary">Continue Shopping</a>
        </div>

        <?php if ($orders_result->num_rows == 0): ?>
            <div class="no-orders">
                <div class="mb-4"><i class="fas fa-shopping-bag fa-5x text-muted"></i></div>
                <h3>You haven't placed any orders yet</h3>
                <p class="text-muted">Once you place an order, you'll be able to track it here.</p>
                <a href="index.php" class="btn btn-primary mt-3">Start Shopping</a>
            </div>
        <?php else: ?>
            <?php while ($order = $orders_result->fetch_assoc()): ?>
                <div class="order-card">
                    <div class="order-header">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="small text-muted">Order Number</div>
                                <div class="font-weight-bold">#<?php echo $order['id']; ?></div>
                            </div>
                            <div class="col-md-3">
                                <div class="small text-muted">Order Date</div>
                                <div><?php echo date('F j, Y', strtotime($order['order_date'])); ?></div>
                            </div>
                            <div class="col-md-3">
                                <div class="small text-muted">Total Amount</div>
                                <div class="font-weight-bold">$<?php echo number_format($order['total_amount'], 2); ?></div>
                            </div>
                            <div class="col-md-3 text-md-right">
                                <div class="small text-muted">Status</div>
                                <span class="status-badge bg-light <?php echo getStatusClass($order['status']); ?>">
                                    <?php echo strtoupper($order['status']); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="order-body">
                        <h5 class="mb-3">Order Items</h5>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th class="text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $order_items = getOrderItems($conn, $order['id']);
                                    $item_count = 0;
                                    while ($item = $order_items->fetch_assoc()):
                                        $item_count += $item['quantity'];
                                    ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <?php if (!empty($item['image'])): ?>
                                                        <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="product-image mr-3">
                                                    <?php endif; ?>
                                                    <div>
                                                        <a href="product.php?id=<?php echo $item['product_id']; ?>" class="font-weight-bold">
                                                            <?php echo htmlspecialchars($item['name']); ?>
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                                            <td><?php echo $item['quantity']; ?></td>
                                            <td class="text-right">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="order-summary">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="order-details">
                                        <div class="order-detail">
                                            <h6>Shipping Address</h6>
                                            <address class="mb-0">
                                                <?php echo htmlspecialchars($order['full_name']); ?><br>
                                                <?php echo htmlspecialchars($order['address']); ?><br>
                                                <?php echo htmlspecialchars($order['city']); ?>, 
                                                <?php echo htmlspecialchars($order['state']); ?> 
                                                <?php echo htmlspecialchars($order['zip_code']); ?><br>
                                                <?php echo htmlspecialchars($order['country']); ?>
                                            </address>
                                        </div>
                                        <div class="order-detail">
                                            <h6>Payment Method</h6>
                                            <p class="mb-0">
                                                <?php 
                                                $payment_method = $order['payment_method'];
                                                $payment_icon = 'credit-card';
                                                
                                                if (strpos($payment_method, 'paypal') !== false) {
                                                    $payment_icon = 'paypal';
                                                } elseif (strpos($payment_method, 'bank') !== false) {
                                                    $payment_icon = 'university';
                                                }
                                                ?>
                                                <i class="fas fa-<?php echo $payment_icon; ?> mr-2"></i>
                                                <?php echo ucwords(str_replace('_', ' ', $payment_method)); ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-md-right">
                                        <div class="mb-1 d-flex justify-content-between">
                                            <span>Subtotal (<?php echo $item_count; ?> items):</span>
                                            <span>$<?php echo number_format($order['total_amount'] - $order['shipping_fee'], 2); ?></span>
                                        </div>
                                        <div class="mb-1 d-flex justify-content-between">
                                            <span>Shipping:</span>
                                            <span>$<?php echo number_format($order['shipping_fee'], 2); ?></span>
                                        </div>
                                        <div class="font-weight-bold d-flex justify-content-between">
                                            <span>Total:</span>
                                            <span>$<?php echo number_format($order['total_amount'], 2); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
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