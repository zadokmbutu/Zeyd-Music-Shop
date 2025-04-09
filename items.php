<?php


// include 'header.php';
// include 'navbar.php';

$products = [
    ["id" => 1, "name" => "Acoustic Guitar", "category" => "Instruments", "price" => 25800],
    ["id" => 2, "name" => "Electric Guitar", "category" => "Instruments", "price" => 45250],
    ["id" => 3, "name" => "Digital Piano", "category" => "Instruments", "price" => 65000],
    ["id" => 4, "name" => "Drum Set", "category" => "Instruments", "price" => 90300],
    ["id" => 5, "name" => "Violin", "category" => "Instruments", "price" => 19400],
    ["id" => 6, "name" => "Guitar Strings", "category" => "Accessories", "price" => 1290],
    ["id" => 7, "name" => "Drum Sticks", "category" => "Accessories", "price" => 1940],
    ["id" => 8, "name" => "Microphone", "category" => "Accessories", "price" => 11640],
    ["id" => 9, "name" => "Classical Music Album", "category" => "Albums", "price" => 1680],
    ["id" => 10, "name" => "Rock Music Album", "category" => "Albums", "price" => 2070]
];
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

    <h1>🎵 Welcome to Our Music Shop 🎶</h1>
    <p>We sell a variety of musical instruments, accessories, and albums.</p>

    <div class="container">
        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Price (Ksh)</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?= htmlspecialchars($product['name']) ?></td>
                        <td><?= htmlspecialchars($product['category']) ?></td>
                        <td><?= number_format($product['price'], 2) ?></td>
                        <td>
                            <button class="cart-btn" onclick="addToCart(<?= $product['id'] ?>)">
                                🛒 Add to Cart
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div>
        <p>🛒 Cart Items: <span id="cart-count"><?= isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0 ?></span></p>
    </div>
                </body>
                </html>

