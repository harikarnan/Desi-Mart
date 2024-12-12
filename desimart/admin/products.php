<?php
require_once './includes/db.php'; // Database connection
require_once './admin_auth.php'; // Authentication middleware

// Fetch all products in ascending order of product_id
$stmt = $db->prepare("
    SELECT products.product_id, products.name, products.price, products.products_image_path, categories.name AS category
    FROM products
    INNER JOIN categories ON products.category_id = categories.category_id
    ORDER BY products.product_id ASC
");
$stmt->execute();
$result = $stmt->get_result();
?>

<?php require_once './includes/header.php'; ?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Manage Products</h2>

    <!-- Add Product Button -->
    <div class="text-end mb-3">
        <a href="add_product.php" class="btn btn-success">Add New Product</a>
    </div>

    <!-- Products Table -->
    <?php if ($result->num_rows > 0): ?>
        <table class="table table-bordered table-striped">
            <thead class="table-primary">
                <tr>
                    <th>Product ID</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Category</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['product_id'] ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td>$<?= number_format($row['price'], 2) ?></td>
                    <td><?= htmlspecialchars($row['category']) ?></td>
                    <td>
                        <?php if (!empty($row['products_image_path'])): ?>
                            <img src="../<?= htmlspecialchars($row['products_image_path']) ?>" alt="<?= htmlspecialchars($row['name']) ?>" style="width: 50px; height: auto;">
                        <?php else: ?>
                            <p>No Image</p>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="edit_product.php?id=<?= $row['product_id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="delete_product.php?id=<?= $row['product_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="text-center">No products available. Click "Add New Product" to create one.</p>
    <?php endif; ?>
</div>

<?php require_once './includes/footer.php'; ?>