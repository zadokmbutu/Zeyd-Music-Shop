<?php
// product.php - Music Shop Products

// Sample product list
$products = [
    ["name" => "Acoustic Guitar", "category" => "Instruments", "price" => 199.99],
    ["name" => "Electric Guitar", "category" => "Instruments", "price" => 349.99],
    ["name" => "Digital Piano", "category" => "Instruments", "price" => 499.99],
    ["name" => "Drum Set", "category" => "Instruments", "price" => 699.99],
    ["name" => "Violin", "category" => "Instruments", "price" => 149.99],
    ["name" => "Guitar Strings", "category" => "Accessories", "price" => 9.99],
    ["name" => "Drum Sticks", "category" => "Accessories", "price" => 14.99],
    ["name" => "Microphone", "category" => "Accessories", "price" => 89.99],
    ["name" => "Classical Music Album", "category" => "Albums", "price" => 12.99],
    ["name" => "Rock Music Album", "category" => "Albums", "price" => 15.99]
];

// Sorting function
$sort = $_GET['sort'] ?? 'name'; // Default sorting by name
usort($products, function ($a, $b) use ($sort) {
    return $a[$sort] <=> $b[$sort];
});
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Music Shop - Products</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f8f8;
            text-align: center;
        }
        h1 {
            background-color: #222;
            color: white;
            padding: 20px;
            margin: 0;
        }
        p {
            font-size: 18px;
            margin: 20px;
        }
        .container {
            width: 90%;
            margin: auto;
            overflow: hidden;
        }
        .search-bar {
            margin: 20px auto;
            width: 50%;
            padding: 10px;
            font-size: 16px;
            border: 2px solid #ff6600;
            border-radius: 5px;
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
            cursor: pointer;
        }
        th:hover {
            background-color: #e65c00;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #ddd;
        }
        @media (max-width: 600px) {
            .search-bar {
                width: 80%;
            }
            table {
                font-size: 14px;
            }
        }
    </style>
    <script>
        function filterProducts() {
            let input = document.getElementById("search").value.toLowerCase();
            let rows = document.querySelectorAll("tbody tr");

            rows.forEach(row => {
                let text = row.innerText.toLowerCase();
                row.style.display = text.includes(input) ? "" : "none";
            });
        }
    </script>
</head>
<body>

    <h1>🎵 Welcome to Our Music Shop 🎶</h1>
    <p>We sell a variety of musical instruments, accessories, and albums.</p>

    <!-- Search Bar -->
    <input type="text" id="search" class="search-bar" onkeyup="filterProducts()" placeholder="🔍 Search products...">

    <div class="container">
        <table>
            <thead>
                <tr>
                    <th><a href="?sort=name" style="color: white; text-decoration: none;">Product Name ⬆</a></th>
                    <th><a href="?sort=category" style="color: white; text-decoration: none;">Category ⬆</a></th>
                    <th><a href="?sort=price" style="color: white; text-decoration: none;">Price ($) ⬆</a></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?= htmlspecialchars($product['name']) ?></td>
                        <td><?= htmlspecialchars($product['category']) ?></td>
                        <td><?= number_format($product['price'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
