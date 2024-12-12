<?php
require_once './includes/db.php'; // Database connection
require_once './admin_auth.php'; // Authentication middleware
require_once './includes/header.php'; // Header with navigation

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];

    // Insert category
    $stmt = $db->prepare("INSERT INTO categories (name) VALUES (?)");
    $stmt->bind_param("s", $name);

    if ($stmt->execute()) {
        header("Location: categories.php");
        exit();
    } else {
        $error = "Failed to add category.";
    }
}
?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Add New Category</h2>

    <!-- Error Message -->
    <?php if (isset($error)): ?>
        <div class="alert alert-danger text-center"><?= $error ?></div>
    <?php endif; ?>

    <!-- Add Category Form -->
    <form method="POST" class="w-50 mx-auto border p-4 rounded shadow-sm">
        <div class="mb-3">
            <label for="name" class="form-label">Category Name</label>
            <input type="text" name="name" id="name" class="form-control" placeholder="Enter category name" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Add Category</button>
    </form>

    <!-- Back to Categories Button -->
    <div class="text-center mt-4">
        <a href="categories.php" class="btn btn-secondary">Back to Categories</a>
    </div>
</div>

<?php require_once './includes/footer.php'; // Footer ?>