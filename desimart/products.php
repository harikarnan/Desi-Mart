<?php
include 'db.php';
$db = (new Database())->getConnection();

// Fetch categories
$query = "SELECT category_id, name FROM categories";
$stmt = $db->prepare($query);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch products based on selected category
$categoryFilter = $_GET['category'] ?? null;
$query = "SELECT product_id, name, price, products_image_path, description FROM products";
if ($categoryFilter) {
    $query .= " WHERE category_id = :category_id";
}
$stmt = $db->prepare($query);
if ($categoryFilter) {
    $stmt->bindParam(':category_id', $categoryFilter, PDO::PARAM_INT);
}
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
    <?php include 'includes/header.php'; ?>
    <main>
        <h1>Products</h1>

        <!-- Filter Section -->
        <section class="filter-section">
        <form method="GET" class="filter-form mb-4">
    <div class="mb-3">
        <label for="category" class="form-label">Filter by Category:</label>
        <select name="category" id="category" class="form-select" onchange="this.form.submit()">
            <option value="">All Categories</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo htmlspecialchars($category['category_id']); ?>" 
                        <?php echo ($category['category_id'] == $categoryFilter) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($category['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</form>
        </section>

        <!-- Product List -->
<div class="row">
    <?php if (empty($products)): ?>
        <p class="col-12">No products found for the selected category.</p>
    <?php else: ?>
        <?php foreach ($products as $product): ?>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                <div class="card">
                    <img src="<?php echo htmlspecialchars($product['products_image_path'] ?: 'images/placeholder.png'); ?>" 
                         class="card-img-top" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                        <p class="card-text">$<?php echo htmlspecialchars(number_format($product['price'], 2)); ?></p>
                        <a href="product_details.php?id=<?php echo htmlspecialchars($product['product_id']); ?>" 
                           class="primary-btn">View Details</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

    </main>
    <?php include 'includes/footer.php'; ?>

