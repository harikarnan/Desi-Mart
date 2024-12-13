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

<?php
include 'includes/db.php'; // Database connection
include 'includes/header.php'; // Header with navigation

// Set number of categories and products per page
$categories_per_page = 6; 
$products_per_page = 6; 

// Get the total number of categories
$category_result = $db->query("SELECT COUNT(*) as total FROM categories");
$total_categories = $category_result->fetch_assoc()['total'];

// Get the total number of products
$product_result = $db->query("SELECT COUNT(*) as total FROM products");
$total_products = $product_result->fetch_assoc()['total'];

// Calculate the total number of pages for categories and products
$total_category_pages = ceil($total_categories / $categories_per_page);
$total_product_pages = ceil($total_products / $products_per_page);

// Get the current page for categories and products, defaulting to page 1
$current_category_page = isset($_GET['category_page']) ? (int)$_GET['category_page'] : 1;
$current_product_page = isset($_GET['product_page']) ? (int)$_GET['product_page'] : 1;

// Calculate the OFFSET for the SQL queries
$category_offset = ($current_category_page - 1) * $categories_per_page;
$product_offset = ($current_product_page - 1) * $products_per_page;

// Fetch categories for the current page
$category_stmt = $db->prepare("SELECT * FROM categories LIMIT ? OFFSET ?");
$category_stmt->bind_param("ii", $categories_per_page, $category_offset);
$category_stmt->execute();
$categories = $category_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch products for the current page
$product_stmt = $db->prepare("SELECT p.*, c.name as category FROM products p LEFT JOIN categories c ON p.category_id = c.category_id LIMIT ? OFFSET ?");
$product_stmt->bind_param("ii", $products_per_page, $product_offset);
$product_stmt->execute();
$products = $product_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

?>

<div class="container mt-5">
<h2 class="text-center mb-4" style="color: #A1351B;">Admin Dashboard</h2>
    <!-- Categories Section -->
    <div class="mb-5">
    <h3 style="color: #A1351B;">Existing Categories</h3>
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

        <!-- Pagination for Categories -->
        <div class="d-flex justify-content-center mt-4">
            <nav aria-label="Category page navigation">
                <ul class="pagination">
                    <li class="page-item <?= $current_category_page == 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="?category_page=<?= $current_category_page - 1 ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    <?php for ($page = 1; $page <= $total_category_pages; $page++): ?>
                        <li class="page-item <?= $page == $current_category_page ? 'active' : '' ?>">
                            <a class="page-link" href="?category_page=<?= $page ?>"><?= $page ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= $current_category_page == $total_category_pages ? 'disabled' : '' ?>">
                        <a class="page-link" href="?category_page=<?= $current_category_page + 1 ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>

    <!-- Products Section -->
    <div>
    <h3 style="color: #A1351B;">Existing Products</h3>
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

        <!-- Pagination for Products -->
        <div class="d-flex justify-content-center mt-4">
            <nav aria-label="Product page navigation">
                <ul class="pagination">
                    <li class="page-item <?= $current_product_page == 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="?product_page=<?= $current_product_page - 1 ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    <?php for ($page = 1; $page <= $total_product_pages; $page++): ?>
                        <li class="page-item <?= $page == $current_product_page ? 'active' : '' ?>">
                            <a class="page-link" href="?product_page=<?= $page ?>"><?= $page ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= $current_product_page == $total_product_pages ? 'disabled' : '' ?>">
                        <a class="page-link" href="?product_page=<?= $current_product_page + 1 ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
