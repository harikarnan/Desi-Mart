<?php
session_start();

$product_id = $_GET['id'] ?? null;
if (!$product_id) {
    die("Invalid product ID.");
}

if (isset($_SESSION['cart'][$product_id])) {
    unset($_SESSION['cart'][$product_id]); // Remove the product from the cart
}

header('Location: cart.php'); // Redirect back to the cart page
exit();
?>
