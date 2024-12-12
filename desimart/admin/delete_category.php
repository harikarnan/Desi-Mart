<?php
require_once './includes/db.php'; // Database connection
require_once './admin_auth.php'; // Authentication middleware

// Get category ID from query parameters
$id = $_GET['id'] ?? null;

// Validate the category ID
if (!$id || !is_numeric($id)) {
    header("Location: categories.php");
    exit();
}

// Prepare the delete query
$stmt = $db->prepare("DELETE FROM categories WHERE category_id = ?");
$stmt->bind_param("i", $id);

// Execute the query and handle the result
if ($stmt->execute()) {
    header("Location: categories.php");
    exit();
} else {
    $error = "Failed to delete category.";
}
?>

<?php require_once './includes/header.php'; ?>

<div class="container mt-5">
    <h2 class="text-center text-danger">Delete Category</h2>

    <!-- Error Message -->
    <?php if (isset($error)): ?>
        <div class="alert alert-danger text-center"><?= $error ?></div>
    <?php else: ?>
        <div class="alert alert-success text-center">
            <p>Category deleted successfully.</p>
            <a href="categories.php" class="btn btn-primary mt-3">Back to Categories</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once './includes/footer.php'; ?>
