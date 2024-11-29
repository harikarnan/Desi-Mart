<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION['admin'])) {
    header('Location: ../admin_login.php');
    exit();
}

$db = (new Database())->getConnection();
$product_id = $_GET['id'];

// Fetch product data
$query = "SELECT * FROM products WHERE product_id = :product_id";
$stmt = $db->prepare($query);
$stmt->execute([':product_id' => $product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch categories for the dropdown
$query = "SELECT * FROM categories";
$stmt = $db->prepare($query);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $category_id = htmlspecialchars($_POST['category_id']);
    $price = htmlspecialchars($_POST['price']);
    $stock_quantity = htmlspecialchars($_POST['stock_quantity']);
    $description = htmlspecialchars($_POST['description']);
    $image_path = htmlspecialchars($_POST['image_path']);

    $query = "UPDATE products SET name = :name, category_id = :category_id, price = :price, 
              stock_quantity = :stock_quantity, description = :description, image_path = :image_path 
              WHERE product_id = :product_id";
    $stmt = $db->prepare($query);
    $stmt->execute([
        ':name' => $name,
        ':category_id' => $category_id,
        ':price' => $price,
        ':stock_quantity' => $stock_quantity,
        ':description' => $description,
        ':image_path' => $image_path,
        ':product_id' => $product_id
    ]);
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link rel="stylesheet" href="../../styles.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <h1>Edit Product</h1>
    <form method="POST">
        <label for="name">Product Name:</label>
        <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>

        <label for="category_id">Category:</label>
        <select name="category_id" id="category_id" required>
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo htmlspecialchars($category['category_id']); ?>"
                    <?php echo $product['category_id'] == $category['category_id'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($category['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="price">Price:</label>
        <input type="number" step="0.01" name="price" id="price" value="<?php echo htmlspecialchars($product['price']); ?>" required>

        <label for="stock_quantity">Stock Quantity:</label>
        <input type="number" name="stock_quantity" id="stock_quantity" value="<?php echo htmlspecialchars($product['stock_quantity']); ?>" required>

        <label for="description">Description:</label>
        <textarea name="description" id="description" required><?php echo htmlspecialchars($product['description']); ?></textarea>

        <label for="image_path">Image Path:</label>
        <input type="text" name="image_path" id="image_path" value="<?php echo htmlspecialchars($product['image_path']); ?>">

        <button type="submit" class="btn">Update Product</button>
    </form>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
