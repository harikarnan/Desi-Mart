<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION['admin'])) {
    header('Location: ../admin_login.php');
    exit();
}

$category_id = $_GET['id'];
$db = (new Database())->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);

    $query = "UPDATE categories SET name = :name WHERE category_id = :category_id";
    $stmt = $db->prepare($query);
    $stmt->execute([':name' => $name, ':category_id' => $category_id]);

    header('Location: index.php');
    exit();
}

$query = "SELECT * FROM categories WHERE category_id = :category_id";
$stmt = $db->prepare($query);
$stmt->execute([':category_id' => $category_id]);
$category = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Category</title>
    <link rel="stylesheet" href="../../styles.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <h1>Edit Category</h1>
    <form method="POST">
        <label for="name">Category Name:</label>
        <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($category['name']); ?>" required>
        <button type="submit" class="btn">Update Category</button>
    </form>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
