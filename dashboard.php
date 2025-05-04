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

// SQL query to fetch the user's details from the 'users' table
$sql = "SELECT * FROM users WHERE user_id = ?";  // Use 'user_id' as the correct column
$stmt = $conn->prepare($sql);  // Prepare the query
$stmt->bind_param("i", $user_id);  // Bind the 'user_id' parameter (i = integer)
$stmt->execute();  // Execute the query

// Get the result and fetch the user's data
$result = $stmt->get_result();
$user = $result->fetch_assoc();  // Fetch the user data from the result set
?>
<style>
    /* Container and Overall Layout */
.dashboard-container {
    max-width: 900px;
    margin: 50px auto;
    padding: 20px;
    background-color: #f9f9f9;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

/* User Details Section */
.user-details {
    background-color: #ffffff;
    padding: 20px;
    margin-bottom: 30px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.user-details h3 {
    color: #333;
}

.detail-item {
    margin: 10px 0;
    font-size: 16px;
    color: #555;
}

.detail-item strong {
    color: #000;
}

/* Payment Section */
.payment-section {
    background-color: #ffffff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.payment-section h4 {
    color: #333;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    font-size: 16px;
    color: #555;
}

.form-group select,
.form-group input {
    width: 100%;
    padding: 10px;
    font-size: 14px;
    border-radius: 5px;
    border: 1px solid #ccc;
}

.form-group select:focus,
.form-group input:focus {
    outline-color: #007bff;
    border-color: #007bff;
}

/* Submit Button */
.btn {
    background-color: #007bff;
    color: white;
    padding: 12px 20px;
    font-size: 16px;
    border-radius: 5px;
    border: none;
    cursor: pointer;
    text-align: center;
}

.btn:hover {
    background-color: #0056b3;
}

/* Logout Button */
.btn-logout {
    background-color: #dc3545;
    color: white;
    padding: 12px 20px;
    font-size: 16px;
    border-radius: 5px;
    border: none;
    margin-top: 20px;
    cursor: pointer;
}

.btn-logout:hover {
    background-color: #c82333;
}

</style>
<!-- HTML Structure with CSS classes -->
<div class="container dashboard-container">
    <div class="user-details">
        <h3>Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h3>
        <p>Here are your account details:</p>
        <div class="detail-item">
            <strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?>
        </div>
        <div class="detail-item">
            <strong>Registered On:</strong> <?php echo htmlspecialchars($user['registration_date']); ?>
        </div>
    </div>

    <div class="payment-section">
        <h4>Make a Payment</h4>
        <!-- Payment Method Selection Form -->
        <form action="payment.php" method="POST">
            <div class="form-group">
                <label for="method">Select Payment Method:</label>
                <select class="form-control" id="method" name="method" required>
                    <option value="">Choose Payment Method</option>
                    <option value="paypal">PayPal</option>
                    <option value="mpesa">M-Pesa</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="amount">Enter Amount:</label>
                <input type="number" class="form-control" id="amount" name="amount" required min="1" step="any">
            </div>
            
            <button type="submit" class="btn btn-primary">Proceed to Payment</button>
        </form>
    </div>

    <button class="btn btn-logout" onclick="location.href='logout.php'">Logout</button>
</div>

<?php include 'footer.php'; ?>
