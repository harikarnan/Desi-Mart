<?php 
require_once './includes/db.php'; // Database connection
require_once './admin_auth.php'; // Authentication middleware

// Get product ID from query parameters
$id = $_GET['id'] ?? null;

// Validate the product ID
if (!$id || !is_numeric($id)) {
    header("Location: products.php");
    exit();
}

// Fetch the product details
$product = $db->query("SELECT * FROM products WHERE product_id = $id")->fetch_assoc();
if (!$product) {
    header("Location: products.php");
    exit();
}

// Fetch all categories for the dropdown
$categories = $db->query("SELECT * FROM categories");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    $description = $_POST['description'];

    // Handle product update
    $stmt = $db->prepare("UPDATE products SET name = ?, price = ?, category_id = ?, description = ? WHERE product_id = ?");
    $stmt->bind_param("sdisi", $name, $price, $category_id, $description, $id);

    if ($stmt->execute()) {
        header("Location: products.php");
        exit();
    } else {
        $error = "Failed to update product.";
    }
}
?>

<?php require_once './includes/header.php'; ?>

<div class="container mt-5">
    <h2 class="text-center mb-4" style="color: #A1351B;">Edit Product</h2>

    <!-- Error Message -->
    <?php if (isset($error)): ?>
        <div class="alert alert-danger text-center"><?= $error ?></div>
    <?php endif; ?>

    <!-- Edit Product Form -->
    <form method="POST" class="w-50 mx-auto border p-4 rounded shadow-sm">
        <div class="mb-3">
            <label for="name" class="form-label">Product Name</label>
            <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="price" class="form-label">Price</label>
            <input type="number" step="0.01" name="price" id="price" class="form-control" value="<?= htmlspecialchars($product['price']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="category" class="form-label">Category</label>
            <select name="category_id" id="category" class="form-select" required>
                <?php while ($row = $categories->fetch_assoc()): ?>
                    <option value="<?= $row['category_id'] ?>" <?= $row['category_id'] == $product['category_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($row['name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" id="description" class="form-control" rows="4" required><?= htmlspecialchars($product['description']) ?></textarea>
        </div>
        <button type="submit" class="btn w-100" style="background-color: #A1351B; color: #fff;">Update Product</button>
    </form>

    <!-- Back to Products Button -->
    <div class="text-center mt-4">
        <a href="products.php" class="btn btn-secondary">Back to Products</a>
    </div>
</div>

<?php require_once './includes/footer.php'; ?>
