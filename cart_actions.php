<?php
session_start();
require 'database.php'; // Using database.php instead of db_connect.php

// Initialize cart if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle adding items to the cart
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = (int)$_POST['quantity'];

    if ($quantity > 0) {
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += $quantity; // Increase quantity if product exists
        } else {
            $_SESSION['cart'][$product_id] = $quantity; // Add new item
        }
    }

    header("Location: cart.php");
    exit();
}

// Handle removing an item
if (isset($_GET['remove'])) {
    $product_id = $_GET['remove'];
    unset($_SESSION['cart'][$product_id]); // Remove item from cart
    header("Location: cart.php");
    exit();
}

// Handle clearing the cart
if (isset($_GET['clear_cart'])) {
    $_SESSION['cart'] = []; // Empty the cart
    header("Location: cart.php");
    exit();
}
?>
