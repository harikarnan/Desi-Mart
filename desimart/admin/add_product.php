<?php
require_once './includes/db.php'; // Database connection
require_once './admin_auth.php'; // Authentication middleware

// Fetch categories for the dropdown
$categories = $db->query("SELECT * FROM categories");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    $description = $_POST['description'];

    // Validate product name (max 30 characters, letters and spaces only)
    if (!preg_match("/^[A-Za-z\s]{1,30}$/", $name)) {
        $error = "Product name must be 1-30 characters long and contain only letters and spaces.";
    }

    // Validate price (4 digit number)
    if (!preg_match("/^\d{1,4}$/", $price)) {
        $error = "Price must be a 4-digit number.";
    }

    // Validate description (max 100 characters)
    if (strlen($description) > 100) {
        $error = "Description must be no longer than 100 characters.";
    }

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
        $stmt = $db->prepare("INSERT INTO products (name, price, category_id, products_image_path, description) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sdiss", $name, $price, $category_id, $image_path, $description);

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
    <!-- Header with Primary CTA Color -->
    <h2 class="text-center mb-4" style="color: #A1351B;">Add New Product</h2>

    <!-- Error Message -->
    <?php if (isset($error)): ?>
        <div class="alert alert-danger text-center"><?= $error ?></div>
    <?php endif; ?>

    <!-- Add Product Form -->
    <form method="POST" enctype="multipart/form-data" class="w-75 mx-auto p-4 border rounded shadow-sm bg-white">
        <div class="mb-3">
            <label for="name" class="form-label fw-bold">Product Name</label>
            <input type="text" name="name" id="name" class="form-control" placeholder="Enter product name" required maxlength="30"
                pattern="^[A-Za-z\s]{1,30}$" title="Product name must be 1-30 characters long and contain only letters and spaces.">
        </div>

        <div class="mb-3">
            <label for="price" class="form-label fw-bold">Price</label>
            <input type="text" name="price" id="price" class="form-control" placeholder="Enter product price" required maxlength="4"
                pattern="^\d{1,4}$" title="Price must be a 4-digit number.">
        </div>

        <div class="mb-3">
            <label for="category" class="form-label fw-bold">Category</label>
            <select name="category_id" id="category" class="form-select" required>
                <option value="" disabled selected>Select Category</option>
                <?php while ($row = $categories->fetch_assoc()): ?>
                    <option value="<?= $row['category_id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="image" class="form-label fw-bold">Product Image</label>
            <input type="file" name="image" id="image" class="form-control" accept="image/*">
        </div>

        <div class="mb-3">
            <label for="description" class="form-label fw-bold">Description</label>
            <textarea name="description" id="description" class="form-control" rows="4" placeholder="Enter product description" required maxlength="100"></textarea>
        </div>

        <div class="button-container">
            <button type="submit" class="primary-btn">Add Product</button>
        </div>
    </form>

    <!-- Back to Products Button -->
    <div class="text-center mt-4">
        <a href="products.php" class="btn btn-secondary">Back to Products</a>
    </div>
</div>

<?php require_once './includes/footer.php'; ?>
