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
$product_id = $_GET['id'] ?? 0;
$products = new Product($db);

// Fetch product details
$query = "SELECT product_id, name, description, price, products_image_path FROM products WHERE product_id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $product_id, PDO::PARAM_INT);
$stmt->execute();
$product = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch featured products
$featuredProducts = $products->getAllProducts(4);

if (!$product) {
    die("Product not found.");
}
?>

<?php include 'includes/header.php'; ?>
<main>
    <section class="container">
        <div class="row align-items-center">
            <div class="col-md-3">
                <img class="rounded w-100 rem-12" src="<?php echo htmlspecialchars($product['products_image_path']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
            </div>
            <div class="col-md-8 ">
                <h2><?php echo htmlspecialchars($product['name']); ?></h2>
                <p class="pt-3"><?php echo htmlspecialchars($product['description']); ?></p>
                <p>Price: $<?php echo htmlspecialchars(number_format($product['price'], 2)); ?></p>
                <a href="add_to_cart.php?id=<?php echo htmlspecialchars($product['product_id']); ?>" class="primary-btn">Add to Cart</a>
            </div>
        </div>
    </section>

    <!-- Featured Offers Section -->
    <section class="featured-offers container mt-5">
        <h2 class="text-center mb-4">Feature Products: </h2>
        <div class="row gy-4">
            <?php if (empty($featuredProducts)): ?>
                <p class="text-center">No products available at the moment.</p>
            <?php else: ?>
                <?php foreach ($featuredProducts as $product): ?>
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                        <div class="card h-100">
                            <img src="<?php echo htmlspecialchars($product['products_image_path'] ?: 'images/placeholder.png'); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>" style="height: 150px; object-fit: cover;">
                            <div class="card-body text-center">
                                <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                <p class="card-text text-muted">$<?php echo htmlspecialchars(number_format($product['price'], 2)); ?></p>
                                <a href="product_details.php?id=<?php echo htmlspecialchars($product['product_id']); ?>" class="btn-sm primary-btn ">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
</main>
<?php include 'includes/footer.php'; ?>