<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION['admin'])) {
    header('Location: ../admin_login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $db = (new Database())->getConnection();

    $query = "INSERT INTO categories (name) VALUES (:name)";
    $stmt = $db->prepare($query);
    $stmt->execute([':name' => $name]);

    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Category</title>
    <link rel="stylesheet" href="../../styles.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <h1>Add Category</h1>
    <form method="POST">
        <label for="name">Category Name:</label>
        <input type="text" name="name" id="name" required>
        <button type="submit" class="btn">Add Category</button>
    </form>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
