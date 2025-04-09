<?php
session_start();

// Initialize cart if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Get product data from the request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = intval($_POST['product_id'] ?? 0);

    if ($product_id > 0) {
        // Check if product is already in cart
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] += 1;
        } else {
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

            if (isset($products[$product_id])) {
                $_SESSION['cart'][$product_id] = [
                    'name' => $products[$product_id]['name'],
                    'price' => $products[$product_id]['price'],
                    'quantity' => 1
                ];
            } else {
                echo json_encode(["status" => "error", "message" => "Product not found"]);
                exit;
            }
        }

        echo json_encode(["status" => "success", "message" => "Product added to cart", "cart_count" => count($_SESSION['cart'])]);
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid product ID"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
}
?>
