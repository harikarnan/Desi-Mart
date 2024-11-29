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
$query = "SELECT product_id, name, price, image_path, description FROM products";
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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DesiMart - Products</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <main>
        <h1>Products</h1>

        <!-- Filter Section -->
        <section class="filter-section">
            <form method="GET" class="filter-form">
                <div class="filter-group">
                    <label for="category">Filter by Category:</label>
                    <select name="category" id="category" onchange="this.form.submit()">
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
        <div class="product-list">
            <?php if (empty($products)): ?>
                <p>No products found for the selected category.</p>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-item">
                        <img src="<?php echo htmlspecialchars($product['image_path'] ?: 'images/placeholder.png'); ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>" style="width:150px; height:auto;">
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p>$<?php echo htmlspecialchars(number_format($product['price'], 2)); ?></p>
                        <a href="product_details.php?id=<?php echo htmlspecialchars($product['product_id']); ?>">View Details</a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
