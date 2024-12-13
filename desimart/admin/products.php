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

<?php
require_once './includes/db.php'; // Database connection
require_once './includes/header.php'; // Header with navigation

// Set number of products per page
$products_per_page = 6; 

// Get the total number of products
$result = $db->query("SELECT COUNT(*) as total FROM products");
$total_products = $result->fetch_assoc()['total'];

// Calculate the total number of pages
$total_pages = ceil($total_products / $products_per_page);

// Get the current page from the query string, defaulting to page 1
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Calculate the OFFSET for the SQL query
$offset = ($current_page - 1) * $products_per_page;

// Fetch products for the current page
$stmt = $db->prepare("SELECT * FROM products LIMIT ? OFFSET ?");
$stmt->bind_param("ii", $products_per_page, $offset);
$stmt->execute();
$products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<div class="container mt-5">
<h2 class="text-center mb-4" style="color: #A1351B;">Manage Products</h2>

    <!-- Add Product Button -->
    <div class="text-end mb-4">
        <a href="add_product.php" class="btn btn-success btn-lg">Add New Product</a>
        
    </div>

    <!-- Products Grid -->
    <div class="row g-4">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <div class="col-md-4 col-sm-6">
                    <div class="card shadow-sm rounded">
                        <!-- Product Image -->
                        <?php if (!empty($product['products_image_path'])): ?>
                            <img src="../<?= $product['products_image_path'] ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>" style="height: 200px; object-fit: cover;">
                        <?php else: ?>
                            <div class="card-img-top bg-light text-center" style="height: 200px; display: flex; justify-content: center; align-items: center;">
                                <span class="text-muted">No Image</span>
                            </div>
                        <?php endif; ?>
                        
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                            <p class="card-text">
                                <small class="text-muted">$<?= number_format($product['price'], 2) ?></small><br>
                            </p>
                            <div class="d-flex justify-content-between">
                                <a href="edit_product.php?id=<?= $product['product_id'] ?>" class="btn btn-warning btn-lg">Edit</a>
                                <a href="delete_product.php?id=<?= $product['product_id'] ?>" class="btn btn-danger btn-lg" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center text-muted w-100">No products available. Click "Add New Product" to create one.</p>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        <nav aria-label="Page navigation">
            <ul class="pagination">
                <!-- Previous Page Link -->
                <li class="page-item <?= $current_page == 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $current_page - 1 ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>

                <!-- Page Numbers -->
                <?php for ($page = 1; $page <= $total_pages; $page++): ?>
                    <li class="page-item <?= $page == $current_page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $page ?>"><?= $page ?></a>
                    </li>
                <?php endfor; ?>

                <!-- Next Page Link -->
                <li class="page-item <?= $current_page == $total_pages ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $current_page + 1 ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</div>

<?php require_once './includes/footer.php'; // Footer ?>
