<?php 
session_start();
include 'header.php';
include 'navbar.php';
include 'database.php';
include 'slideshow.php';
include 'items.php';

// Personalized Product Fetch Logic (Step 2)
$user_id = $_SESSION['user_id'] ?? null;

// Default query if no user or no preferences
$query = "SELECT * FROM products ORDER BY RAND() LIMIT 10";

// Personalization logic
if ($user_id) {
    $stmt = $conn->prepare("SELECT favorite_category FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($favorite_category);
    if ($stmt->fetch() && !empty($favorite_category)) {
        $query = "SELECT * FROM products WHERE category = '$favorite_category' ORDER BY RAND() LIMIT 10";
    }
    $stmt->close();
}

// Fetch products from the database
$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Music Shop - Products</title>
    <script>
        function addToCart(productId) {
            fetch('update_cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `product_id=${productId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('Product added to cart!');
                    document.getElementById('cart-count').textContent = data.cart_count;
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        }
    </script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f8f8;
            text-align: center;
        }
        h1 {
            background-color: #222;
            color: white;
            padding: 20px;
        }
        .container {
            width: 90%;
            margin: auto;
            overflow: hidden;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            padding: 15px;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #ff6600;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #ddd;
        }
        .cart-btn {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 8px 12px;
            cursor: pointer;
            font-size: 14px;
            border-radius: 5px;
        }
        .cart-btn:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

</body>
</html>

<?php 
$conn->close();
include 'footer.php'; 
?>
