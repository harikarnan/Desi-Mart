<?php
require_once './includes/db.php'; // Database connection
require_once './admin_auth.php'; // Authentication middleware
require_once '../classes/Sanitizer.php';


$sanitize_input = new Sanitizer();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize form data
    $name = $sanitize_input->sanitize_input($_POST['name']);
    $image = $_FILES['image'];

    // Validate category name with regex (only letters and spaces)
    if (!preg_match("/^[A-Za-z \s]+$/", $name)) {
        echo "<p style='color: red;'>Error: Category name can only contain letters and spaces.</p>";
    } else {
        // Handle image upload
        $image_path = null;
        if (!empty( $sanitize_input->sanitize_input($image['name'])) ) {
            $target_dir = "../images/categories/";
            $image_name = basename( $sanitize_input->sanitize_input($image['name']));
            $target_file = $target_dir . $image_name;

            // Validate and move the uploaded file
            if (move_uploaded_file($image['tmp_name'], $target_file)) {
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
}
?>

<?php require_once './includes/header.php'; ?>

<div class="container mt-5">
    <h2 class="text-center mb-4" style="color: #A1351B;">Add New Category</h2>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger text-center"><?= $error ?></div>
    <?php endif; ?>

    <!-- Add Category Form -->
    <form method="POST" enctype="multipart/form-data" class="w-75 mx-auto p-4 border rounded shadow-sm bg-white">
        <div class="mb-3">
            <label for="name" class="form-label fw-bold">Category Name</label>
            <input 
                type="text" 
                name="name" 
                id="name" 
                class="form-control" 
                placeholder="Enter category name" 
                required 
                maxlength="30" 
                pattern="^[A-Za-z\s]+$" 
                title="Only letters and spaces are allowed, with a maximum length of 30 characters."
            >
        </div>

        <div class="mb-3">
            <label for="image" class="form-label fw-bold">Category Image</label>
            <input type="file" name="image" id="image" class="form-control" accept="image/*">
            <small class="form-text text-muted">Max file size: 2MB. Accepted formats: JPG, PNG, GIF.</small>
        </div>

        <div class="button-container">
            <button type="submit" class="primary-btn">Add Category</button>
        </div>
    </form>

    <div class="text-center mt-4">
        <a href="categories.php" class="btn btn-secondary">Back to Categories</a>
    </div>
</div>

<?php require_once './includes/footer.php'; ?>
