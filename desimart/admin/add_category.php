<?php
require_once './includes/db.php'; // Database connection
require_once './admin_auth.php'; // Authentication middleware

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];

    // Handle image upload
    $image_path = null;
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "../images/categories/";
        $image_name = basename($_FILES['image']['name']);
        $target_file = $target_dir . $image_name;

        // Validate and move the uploaded file
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_path = "images/categories/" . $image_name; // Relative path for storing in the database
        } else {
            $error = "Failed to upload category image.";
        }
    }

    // Insert category if no errors
    if (!isset($error)) {
        $stmt = $db->prepare("INSERT INTO categories (name, categories_image_path) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $image_path);

        if ($stmt->execute()) {
            header("Location: categories.php");
            exit();
        } else {
            $error = "Failed to add category.";
        }
    }
}
?>

<?php require_once './includes/header.php'; ?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Add New Category</h2>

    <!-- Error Message -->
    <?php if (isset($error)): ?>
        <div class="alert alert-danger text-center"><?= $error ?></div>
    <?php endif; ?>

    <!-- Add Category Form -->
    <form method="POST" enctype="multipart/form-data" class="w-50 mx-auto border p-4 rounded shadow-sm">
        <div class="mb-3">
            <label for="name" class="form-label">Category Name</label>
            <input type="text" name="name" id="name" class="form-control" placeholder="Enter category name" required>
        </div>
        <div class="mb-3">
            <label for="image" class="form-label">Category Image</label>
            <input type="file" name="image" id="image" class="form-control" accept="image/*">
        </div>
        <button type="submit" class="btn btn-primary w-100">Add Category</button>
    </form>

    <!-- Back to Categories Button -->
    <div class="text-center mt-4">
        <a href="categories.php" class="btn btn-secondary">Back to Categories</a>
    </div>
</div>

<?php require_once './includes/footer.php'; ?>