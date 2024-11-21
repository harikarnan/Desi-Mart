<?php
require_once 'db.php';
require_once 'classes/Product.php';

$db = (new Database())->getConnection();
$product = new Product($db);

// Fetch featured products
$featuredProducts = $product->getAllProducts();
?>
<?php include 'includes/header.php'; ?>
<h2>Featured Products</h2>
<div class="product-list">
    <?php if (empty($featuredProducts)): ?>
        <p>No products available at the moment.</p>
    <?php else: ?>
        <?php foreach ($featuredProducts as $product): ?>
            <div class="product-item">
                <img src="images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                <p>$<?php echo htmlspecialchars(number_format($product['price'], 2)); ?></p>
                <a href="product_details.php?id=<?php echo htmlspecialchars($product['id']); ?>">View Details</a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>
