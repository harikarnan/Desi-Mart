<?php
session_start();

$product_id = $_GET['id'] ?? null;
if (!$product_id) {
    die("Invalid product ID.");
}

// Check if the product exists in the cart and remove it
if (isset($_SESSION['cart'][$product_id])) {
    unset($_SESSION['cart'][$product_id]);
}

header('Location: cart.php'); // Redirect back to cart page
exit();
?>
