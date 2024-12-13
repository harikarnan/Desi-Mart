<?php
require_once './includes/db.php'; // Database connection
require_once './admin_auth.php'; // Authentication middleware
require_once '../classes/Sanitizer.php';


$sanitize_input = new Sanitizer();

// Get category ID from query parameters
$id = $_GET['id'] ?? null;

// Validate the category ID
if (!$id || !is_numeric($id)) {
    header("Location: categories.php");
    exit();
}

// Fetch the category details
$category = $db->query("SELECT * FROM categories WHERE category_id = $id")->fetch_assoc();
if (!$category) {
    header("Location: categories.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $sanitize_input->sanitize_input($_POST['name']);

    // Update category
    $stmt = $db->prepare("UPDATE categories SET name = ? WHERE category_id = ?");
    $stmt->bind_param("si", $name, $id);

    if ($stmt->execute()) {
        header("Location: categories.php");
        exit();
    } else {
        $error = "Failed to update category.";
    }
}
?>

<?php require_once './includes/header.php'; ?>

<div class="container mt-5">
    <h2 class="text-center mb-4" style="color: #A1351B;">Edit Category</h2>

    <!-- Error Message -->
    <?php if (isset($error)): ?>
        <div class="alert alert-danger text-center"><?= $error ?></div>
    <?php endif; ?>

    <!-- Edit Category Form -->
    <form method="POST" class="w-50 mx-auto border p-4 rounded shadow-sm">
        <div class="mb-3">
            <label for="name" class="form-label">Category Name</label>
            <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($category['name']) ?>" required>
        </div>
        <button type="submit" class="btn w-100" style="background-color: #A1351B; color: #fff;">Update Category</button>
    </form>

    <!-- Back to Categories Button -->
    <div class="text-center mt-4">
        <a href="categories.php" class="btn btn-secondary">Back to Categories</a>
    </div>
</div>

<?php require_once './includes/footer.php'; ?>
