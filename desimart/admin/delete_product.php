<?php
require_once './includes/db.php'; // Database connection
require_once './admin_auth.php'; // Authentication middleware

// Get product ID from query parameters
$id = $_GET['id'] ?? null;

// Validate the product ID
if (!$id || !is_numeric($id)) {
    header("Location: products.php");
    exit();
}

// Fetch the product details to get the image path
$stmt = $db->prepare("SELECT products_image_path FROM products WHERE product_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

// Check if the product exists
if (!$product) {
    header("Location: products.php");
    exit();
}

// Delete the image file if it exists
if (!empty($product['products_image_path'])) {
    $image_path = "../" . $product['products_image_path']; // Construct the full path to the image
    if (file_exists($image_path)) {
        unlink($image_path); // Delete the image file
    }
}

// Delete the product from the database
$stmt = $db->prepare("DELETE FROM products WHERE product_id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: products.php");
    exit();
} else {
    echo "Failed to delete product.";
}
?>
