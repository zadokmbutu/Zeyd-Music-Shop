<?php
session_start();
include 'header.php';  // Include your header or navigation bar
include 'database.php';  // Include your database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");  // Redirect to login page if the user is not logged in
    exit();
}

$user_id = $_SESSION['user_id'];  // Get the logged-in user's ID from the session

// Get the payment method and amount from the form submission
$payment_method = $_POST['method'];
$amount = $_POST['amount'];

// Simulate the payment processing based on the selected method
if ($payment_method == 'paypal') {
    // Here, you would typically use PayPal API to process the payment
    // Simulate a successful PayPal payment
    echo "<h3>Processing PayPal Payment...</h3>";
    echo "<p>You are about to pay <strong>$" . htmlspecialchars($amount) . "</strong> using PayPal.</p>";
    echo "<p>Redirecting to PayPal...</p>";
    // You can add PayPal API code here or a redirect to PayPal payment page

} elseif ($payment_method == 'mpesa') {
    // Here, you would typically integrate M-Pesa API for payment processing
    // Simulate a successful M-Pesa payment
    echo "<h3>Processing M-Pesa Payment...</h3>";
    echo "<p>You are about to pay <strong>KSh " . htmlspecialchars($amount) . "</strong> using M-Pesa.</p>";
    echo "<p>Redirecting to M-Pesa...</p>";
    // You can add M-Pesa API code here or a redirect to M-Pesa payment page

} else {
    echo "<p>Invalid payment method selected.</p>";
    exit();
}

// Insert payment details into the database
$sql = "INSERT INTO payments (user_id, method, amount) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isd", $user_id, $payment_method, $amount);
$stmt->execute();  // Execute the payment record insertion

?>

<!-- Add a thank you message after the payment -->
<div class="container">
    <h3>Thank you for your payment!</h3>
    <p>Your payment of <?php echo htmlspecialchars($amount); ?> via <?php echo ucfirst($payment_method); ?> was successful.</p>
    <button class="btn btn-primary" onclick="location.href='dashboard.php'">Return to Dashboard</button>
</div>

<?php include 'footer.php'; ?>
