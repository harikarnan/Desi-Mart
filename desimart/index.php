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
$limit = 5;
$query = "SELECT * FROM categories ORDER BY RAND()
            LIMIT $limit";
$stmt = $db->prepare($query);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch featured products
$featuredProducts = $product->getAllProducts(4);
?>

<?php include 'includes/header.php'; ?>
<main class="container-fluid p-0">
    <!-- Banner Section -->
    <section class="banner">
    <img src="images/banner.jpg" alt="Banner Image" class="img-fluid w-100">
    <div class="banner-overlay">
        <div class="banner-text">
            <h1 class="fw-bold display-4 text-center">Welcome to DesiMart</h1>
            <p class="lead text-center">Your one-stop shop for authentic products</p>
        </div>
    </div>
</section>



    <!-- Featured Categories Section -->
    <section id="categories" class="categories container my-5">
    <h2 class="text-center mb-4">Featured Categories</h2>
    <div class="categories-container">
        <?php foreach ($categories as $category): ?>
            <div class="category-item">
                <a href="products.php?category=<?php echo htmlspecialchars($category['category_id']); ?>" class="text-decoration-none">
                    <div class="category-circle">
                        <img src="<?php echo htmlspecialchars($category['categories_image_path']); ?>" alt="<?php echo htmlspecialchars($category['name']); ?>" class="img-fluid">
                    </div>
                    <span><?php echo htmlspecialchars($category['name']); ?></span>
                </a>
            </div>
        <?php endforeach; ?>
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
