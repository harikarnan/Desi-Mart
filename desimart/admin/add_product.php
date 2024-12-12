<?php
require_once './includes/db.php'; // Database connection
require_once './admin_auth.php'; // Authentication middleware

// Fetch categories for the dropdown
$categories = $db->query("SELECT * FROM categories");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];

    // Handle image upload
    $image_path = null;
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "../images/products/";
        $image_name = basename($_FILES['image']['name']);
        $target_file = $target_dir . $image_name;

        // Validate and move the uploaded file
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_path = "images/products/" . $image_name; // Relative path for storing in the database
        } else {
            $error = "Failed to upload image.";
        }
    }

    // Insert product if no errors
    if (!isset($error)) {
        $stmt = $db->prepare("INSERT INTO products (name, price, category_id, products_image_path) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sdis", $name, $price, $category_id, $image_path); // Corrected type definition string

        if ($stmt->execute()) {
            header("Location: products.php");
            exit();
        } else {
            $error = "Failed to add product.";
        }
    }
}
?>

<?php require_once './includes/header.php'; ?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Add New Product</h2>

    <!-- Error Message -->
    <?php if (isset($error)): ?>
        <div class="alert alert-danger text-center"><?= $error ?></div>
    <?php endif; ?>

    <!-- Add Product Form -->
    <form method="POST" enctype="multipart/form-data" class="w-50 mx-auto border p-4 rounded shadow-sm">
        <div class="mb-3">
            <label for="name" class="form-label">Product Name</label>
            <input type="text" name="name" id="name" class="form-control" placeholder="Enter product name" required>
        </div>
        <div class="mb-3">
            <label for="price" class="form-label">Price</label>
            <input type="number" step="0.01" name="price" id="price" class="form-control" placeholder="Enter product price" required>
        </div>
        <div class="mb-3">
            <label for="category" class="form-label">Category</label>
            <select name="category_id" id="category" class="form-select" required>
                <option value="" disabled selected>Select Category</option>
                <?php while ($row = $categories->fetch_assoc()): ?>
                    <option value="<?= $row['category_id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="image" class="form-label">Product Image</label>
            <input type="file" name="image" id="image" class="form-control" accept="image/*">
        </div>
        <button type="submit" class="btn btn-primary w-100">Add Product</button>
    </form>

    <!-- Back to Products Button -->
    <div class="text-center mt-4">
        <a href="products.php" class="btn btn-secondary">Back to Products</a>
    </div>
</div>

<?php require_once './includes/footer.php'; ?>
