<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION['admin'])) {
    header('Location: ../admin_login.php');
    exit();
}

$db = (new Database())->getConnection();

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

    $query = "INSERT INTO products (name, category_id, price, stock_quantity, description, image_path) 
              VALUES (:name, :category_id, :price, :stock_quantity, :description, :image_path)";
    $stmt = $db->prepare($query);
    $stmt->execute([
        ':name' => $name,
        ':category_id' => $category_id,
        ':price' => $price,
        ':stock_quantity' => $stock_quantity,
        ':description' => $description,
        ':image_path' => $image_path
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
    <title>Add Product</title>
    <link rel="stylesheet" href="../../styles.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <h1>Add Product</h1>
    <form method="POST">
        <label for="name">Product Name:</label>
        <input type="text" name="name" id="name" required>

        <label for="category_id">Category:</label>
        <select name="category_id" id="category_id" required>
            <option value="">Select a Category</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo htmlspecialchars($category['category_id']); ?>">
                    <?php echo htmlspecialchars($category['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="price">Price:</label>
        <input type="number" step="0.01" name="price" id="price" required>

        <label for="stock_quantity">Stock Quantity:</label>
        <input type="number" name="stock_quantity" id="stock_quantity" required>

        <label for="description">Description:</label>
        <textarea name="description" id="description" required></textarea>

        <label for="image_path">Image Path:</label>
        <input type="text" name="image_path" id="image_path">

        <button type="submit" class="btn">Add Product</button>
    </form>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
