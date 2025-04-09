<?php
session_start();  // Start session to manage cart
include 'header.php';
include 'navbar.php';

// Sample product data (replace with data from your database)
$products = [
    [
        "id" => 1,
        "name" => "Acoustic Guitar",
        "category" => "Instruments",
        "price" => 25800,
        "description" => "A classic acoustic guitar with a rich tone and great playability.",
        "image" => "images/acoustic_guitar.png"
    ],
    [
        "id" => 2,
        "name" => "Electric Guitar",
        "category" => "Instruments",
        "price" => 45250,
        "description" => "A high-quality electric guitar for rock and blues enthusiasts.",
        "image" => "images/electric_guitar.png"
    ],
    [
        "id" => 3,
        "name" => "Digital Piano",
        "category" => "Instruments",
        "price" => 65000,
        "description" => "A digital piano with a realistic touch and sound for aspiring pianists.",
        "image" => "images/digital_piano.png"
    ],
    [
        "id" => 4,
        "name" => "Drum Set",
        "category" => "Instruments",
        "price" => 90300,
        "description" => "A complete drum set perfect for rock and jazz drummers.",
        "image" => "images/drum_set.png"
    ],
    [
        "id" => 5,
        "name" => "Violin",
        "category" => "Instruments",
        "price" => 19400,
        "description" => "A violin with a smooth, sweet tone, perfect for classical music.",
        "image" => "images/violin.png"
    ],
    [
        "id" => 6,
        "name" => "Saxophone",
        "category" => "Instruments",
        "price" => 35000,
        "description" => "A high-quality saxophone for jazz and classical musicians.",
        "image" => "images/saxophone.png"
    ],
    [
        "id" => 7,
        "name" => "Trumpet",
        "category" => "Instruments",
        "price" => 27000,
        "description" => "A professional trumpet with a bright, crisp tone.",
        "image" => "images/trumpet.png"
    ],
    [
        "id" => 8,
        "name" => "Flute",
        "category" => "Instruments",
        "price" => 22000,
        "description" => "A beautiful wooden flute with a smooth, clear sound.",
        "image" => "images/flute.png"
    ],
    [
        "id" => 9,
        "name" => "Cello",
        "category" => "Instruments",
        "price" => 80000,
        "description" => "A cello with a deep, rich tone perfect for orchestral music.",
        "image" => "images/cello.png"
    ],
    [
        "id" => 10,
        "name" => "Roli pad",
        "category" => "Instruments",
        "price" => 45000,
        "description" => "An electric keyboard with multiple voices and effects for modern musicians.",
        "image" => "images/roli_pad.png"
    ]
];

// Get the search query from the GET request
$search_query = isset($_GET['search']) ? strtolower(trim($_GET['search'])) : '';

// Filter products based on the search query (case-insensitive)
if ($search_query) {
    $filtered_products = array_filter($products, function($product) use ($search_query) {
        return strpos(strtolower($product['name']), $search_query) !== false;
    });
} else {
    $filtered_products = $products;  // Show all products if no search query is provided
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
        .product-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            margin: 20px 0;
        }
        .product-card {
            width: 22%;
            margin: 10px;
            background-color: white;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
            border-radius: 8px;
            transition: transform 0.3s;
        }
        .product-card:hover {
            transform: scale(1.05);
        }
        .product-card img {
            width: 200px;  /* Set a fixed width for all images */
            height: 200px; /* Set a fixed height to ensure uniformity */
            object-fit: cover; /* Ensures the image fits within the specified size without distorting */
            border-radius: 5px;
            margin-bottom: 10px;
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
        .description {
            font-size: 14px;
            color: #555;
            margin-top: 10px;
            font-style: italic;
        }
        .price {
            font-weight: bold;
            margin-top: 10px;
            font-size: 16px;
        }
        .search-bar {
            margin: 20px 0;
        }
    </style>
</head>
<body>

    <h1>🎵 Welcome to Our Music Shop 🎶</h1>
    <p>We sell a variety of musical instruments and accessories.</p>

    <!-- Search Bar -->
    <div class="search-bar">
        <form method="get" action="products.php">
            <input type="text" name="search" placeholder="Search for instruments..." class="form-control" style="width: 300px; display: inline-block;">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
    </div>

    <div class="container">
        <div class="product-row">
            <?php if (empty($filtered_products)): ?>
                <p>No products found for your search.</p>
            <?php else: ?>
                <?php foreach ($filtered_products as $product): ?>
                    <div class="product-card">
                        <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                        <h3><?= htmlspecialchars($product['name']) ?></h3>
                        <p><strong>Category:</strong> <?= htmlspecialchars($product['category']) ?></p>
                        <p class="price">Ksh <?= number_format($product['price'], 2) ?></p>
                        <p class="description"><?= htmlspecialchars($product['description']) ?></p>
                        <button class="cart-btn" onclick="addToCart(<?= $product['id'] ?>)">🛒 Add to Cart</button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div>
        <p>🛒 Cart Items: <span id="cart-count"><?= isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0 ?></span></p>
    </div>

</body>
</html>
<?php include 'footer.php'; ?>
