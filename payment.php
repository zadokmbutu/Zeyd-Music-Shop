<?php
session_start();
include 'header.php';
include 'navbar.php';
include 'database.php';  // Ensure this includes your correct DB connection details

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");  // Redirect to login if the user is not logged in
    exit();
}

$user_id = $_SESSION['user_id'];  // Get the logged-in user's ID from the session

// Fetch the amount and payment method from the form submission
$payment_method = $_POST['method'];
$amount = $_POST['amount'];

// Insert payment details into the database (before redirecting to payment gateways)
$sql = "INSERT INTO payments (user_id, method, amount) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isd", $user_id, $payment_method, $amount);
$stmt->execute();  // Execute the payment record insertion

// Simulate the payment processing based on the selected method
if ($payment_method == 'paypal') {
    // PayPal integration
    // PayPal Payment Integration (Using PayPal Checkout)
    $client_id = 'your_paypal_client_id';  // Replace with your PayPal Client ID
    $secret = 'your_paypal_secret';  // Replace with your PayPal Secret
    $return_url = 'https://yourwebsite.com/confirmation.php';  // Redirect URL after payment success
    $cancel_url = 'https://yourwebsite.com/cancel.php';  // Redirect URL if payment is canceled

    // Create a PayPal order and redirect to PayPal
    $paypal_url = "https://www.paypal.com/checkoutnow?token=";  // PayPal's checkout URL
    $paypal_url .= $client_id . "&return_url=" . urlencode($return_url) . "&cancel_url=" . urlencode($cancel_url);
    
    echo "<h3>Processing PayPal Payment...</h3>";
    echo "<p>You are about to pay <strong>$" . htmlspecialchars($amount) . "</strong> using PayPal.</p>";
    echo "<p>Redirecting to PayPal...</p>";
    header("Location: $paypal_url");  // Redirect to PayPal

} elseif ($payment_method == 'mpesa') {
    // M-Pesa Integration using Safaricom's Daraja API
    // You'll need to get the proper credentials and setup with Safaricom

    $shortcode = 'your_shortcode';  // Replace with your M-Pesa shortcode
    $lipa_na_mpesa_online_shortcode = 'your_shortcode';  // Replace with the shortcode for online payments
    $lipa_na_mpesa_online_shortcode_password = 'your_password';  // Replace with the password for the shortcode

    $lipa_na_mpesa_online_url = 'https://www.safaricom.co.ke/mpesa/online/urls';  // The M-Pesa API URL

    // M-Pesa Payment Integration logic
    $mpesa_url = "https://www.safaricom.co.ke/mpesa/online/checkout?amount=" . $amount . "&shortcode=" . $lipa_na_mpesa_online_shortcode;
    echo "<h3>Processing M-Pesa Payment...</h3>";
    echo "<p>You are about to pay <strong>KSh " . htmlspecialchars($amount) . "</strong> using M-Pesa.</p>";
    echo "<p>Redirecting to M-Pesa...</p>";
    header("Location: $mpesa_url");  // Redirect to M-Pesa API

} else {
    echo "<p>Invalid payment method selected.</p>";
    exit();
}

?>

<!-- Add a thank you message after the payment -->
<div class="container">
    <h3>Thank you for your payment!</h3>
    <p>Your payment of <?php echo htmlspecialchars($amount); ?> via <?php echo ucfirst($payment_method); ?> is being processed.</p>
    <button class="btn btn-primary" onclick="location.href='dashboard.php'">Return to Dashboard</button>
</div>

<?php include 'footer.php'; ?>
