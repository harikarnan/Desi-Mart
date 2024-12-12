<?php
session_start();
require_once 'db.php';

$product_id = $_POST['product_id'] ?? null;
$quantity = $_POST['quantity'] ?? 1;

if (!$product_id) {
    die("Invalid product ID.");
}

$db = (new Database())->getConnection();

// Fetch product details from the database
$query = "SELECT product_id, name, price, products_image_path FROM products WHERE product_id = :product_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
$stmt->execute();
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die("Product not found.");
}

// Initialize the cart if not already set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Check if the product is already in the cart
if (isset($_SESSION['cart'][$product_id])) {
    $_SESSION['cart'][$product_id]['quantity'] = min(8, $_SESSION['cart'][$product_id]['quantity'] + $quantity); // Increment quantity
} else {
    // Add new product to the cart
    $_SESSION['cart'][$product_id] = [
        'name' => $product['name'],
        'price' => $product['price'],
        'quantity' => 1, // Default quantity
        'products_image_path' => $product['products_image_path']
    ];
}

header('Location: product_details.php'); // Redirect back to product_details page

exit();
?>