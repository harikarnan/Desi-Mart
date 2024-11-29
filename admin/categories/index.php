<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION['admin'])) {
    header('Location: ../admin_login.php');
    exit();
}

$db = (new Database())->getConnection();

$query = "SELECT * FROM categories";
$stmt = $db->prepare($query);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories</title>
    <link rel="stylesheet" href="../../styles.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <h1>Manage Categories</h1>
    <a href="add.php" class="btn">Add Category</a>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $category): ?>
                <tr>
                    <td><?php echo htmlspecialchars($category['category_id']); ?></td>
                    <td><?php echo htmlspecialchars($category['name']); ?></td>
                    <td>
                        <a href="edit.php?id=<?php echo $category['category_id']; ?>" class="btn">Edit</a>
                        <a href="delete.php?id=<?php echo $category['category_id']; ?>" class="btn">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
