<?php
session_start();

// Redirect to login page if the user is not logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

require_once 'db.php';
require_once 'classes/Product.php';

$db = (new Database())->getConnection();
$product = new Product($db);

// Fetch categories
$query = "SELECT * FROM categories";
$stmt = $db->prepare($query);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch featured products
$featuredProducts = $product->getAllProducts();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DesiMart - Home</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="page-container">
        <?php include 'includes/header.php'; ?>
        <main>
            <h1>Welcome to DesiMart</h1>
            
            <!-- Categories Section -->
            <section class="categories">
                <h2>Categories</h2>
                <div class="category-list">
                    <?php foreach ($categories as $category): ?>
                        <a href="products.php?category=<?php echo htmlspecialchars($category['category_id']); ?>" class="category-item">
                            <?php echo htmlspecialchars($category['name']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
            
            <!-- Featured Products Section -->
            <section class="featured-products">
                <h2>Featured Products</h2>
                <div class="product-list">
                    <?php if (empty($featuredProducts)): ?>
                        <p>No products available at the moment.</p>
                    <?php else: ?>
                        <?php foreach ($featuredProducts as $product): ?>
                            <div class="product-item">
                                <img src="<?php echo htmlspecialchars($product['image_path'] ?: 'images/placeholder.png'); ?>" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>" style="width:150px; height:auto;">
                                <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                                <p>$<?php echo htmlspecialchars(number_format($product['price'], 2)); ?></p>
                                <p><?php echo htmlspecialchars(substr($product['description'], 0, 100)) . '...'; ?></p>
                                <a href="product_details.php?id=<?php echo htmlspecialchars($product['product_id']); ?>">View Details</a>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>
        </main>
        <?php include 'includes/footer.php'; ?>
    </div>
</body>
</html>