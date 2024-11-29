<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION['admin'])) {
    header('Location: ../admin_login.php');
    exit();
}

$db = (new Database())->getConnection();

// Fetch products
$query = "SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON p.category_id = c.category_id";
$stmt = $db->prepare($query);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
    <link rel="stylesheet" href="../../styles.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <h1>Manage Products</h1>
    <a href="add.php" class="btn">Add New Product</a>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?php echo htmlspecialchars($product['product_id']); ?></td>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                    <td>$<?php echo htmlspecialchars(number_format($product['price'], 2)); ?></td>
                    <td><?php echo htmlspecialchars($product['stock_quantity']); ?></td>
                    <td>
                        <a href="edit.php?id=<?php echo $product['product_id']; ?>" class="btn">Edit</a>
                        <a href="delete.php?id=<?php echo $product['product_id']; ?>" class="btn">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
