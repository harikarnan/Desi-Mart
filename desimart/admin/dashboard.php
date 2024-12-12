<?php
require_once 'includes/db.php';
require_once 'admin_auth.php';

// Fetch categories in ascending order of category_id
$categoriesStmt = $db->query("SELECT * FROM categories ORDER BY category_id ASC");
$categories = $categoriesStmt->fetch_all(MYSQLI_ASSOC);

// Fetch products in ascending order of product_id
$productsStmt = $db->query("
    SELECT products.product_id, products.name, products.price, products.products_image_path, categories.name AS category 
    FROM products 
    INNER JOIN categories ON products.category_id = categories.category_id
    ORDER BY products.product_id ASC
");
$products = $productsStmt->fetch_all(MYSQLI_ASSOC);
?>

<?php include 'includes/header.php'; ?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Admin Dashboard</h2>

    <!-- Categories Section -->
    <div class="mb-5">
        <h3>Existing Categories</h3>
        <?php if (!empty($categories)): ?>
            <table class="table table-bordered">
                <thead class="table-primary">
                    <tr>
                        <th>Category ID</th>
                        <th>Name</th>
                        <th>Image</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category): ?>
                        <tr>
                            <td><?= $category['category_id'] ?></td>
                            <td><?= htmlspecialchars($category['name']) ?></td>
                            <td>
                                <?php if (!empty($category['categories_image_path'])): ?>
                                    <img src="../<?= $category['categories_image_path'] ?>" alt="<?= $category['name'] ?>" style="width: 50px; height: auto;">
                                <?php else: ?>
                                    <p>No Image</p>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No categories available.</p>
        <?php endif; ?>
    </div>

    <!-- Products Section -->
    <div>
        <h3>Existing Products</h3>
        <?php if (!empty($products)): ?>
            <table class="table table-bordered">
                <thead class="table-primary">
                    <tr>
                        <th>Product ID</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Category</th>
                        <th>Image</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?= $product['product_id'] ?></td>
                            <td><?= htmlspecialchars($product['name']) ?></td>
                            <td>$<?= number_format($product['price'], 2) ?></td>
                            <td><?= htmlspecialchars($product['category']) ?></td>
                            <td>
                                <?php if (!empty($product['products_image_path'])): ?>
                                    <img src="../<?= $product['products_image_path'] ?>" alt="<?= $product['name'] ?>" style="width: 50px; height: auto;">
                                <?php else: ?>
                                    <p>No Image</p>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No products available.</p>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
