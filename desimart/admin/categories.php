<?php
require_once './includes/db.php'; // Database connection
require_once 'admin_auth.php';


// Fetch all categories
$stmt = $db->query("SELECT * FROM categories");
$categories = $stmt->fetch_all(MYSQLI_ASSOC);

// Set number of categories per page
$categories_per_page = 6; 

// Get the total number of categories
$result = $db->query("SELECT COUNT(*) as total FROM categories");
$total_categories = $result->fetch_assoc()['total'];

// Calculate the total number of pages
$total_pages = ceil($total_categories / $categories_per_page);

// Get the current page from the query string, defaulting to page 1
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Calculate the OFFSET for the SQL query
$offset = ($current_page - 1) * $categories_per_page;

// Fetch categories for the current page
$stmt = $db->prepare("SELECT * FROM categories LIMIT ? OFFSET ?");
$stmt->bind_param("ii", $categories_per_page, $offset);
$stmt->execute();
$categories = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<?php include 'includes/header.php'; ?>
<div class="container mt-5">
<h2 class="text-center mb-4" style="color: #A1351B;">Manage Categories</h2>


    <!-- Add Category Button -->
    <div class="text-end mb-4">
        <a href="./add_category.php" class="btn btn-success btn-lg">Add New Category</a>
    </div>

    <!-- Categories Grid -->
    <div class="row g-4">
        <?php if (!empty($categories)): ?>
            <?php foreach ($categories as $category): ?>
                <div class="col-md-4 col-sm-6">
                    <div class="card shadow-sm rounded">
                        <!-- Category Image -->
                        <?php if (!empty($category['categories_image_path'])): ?>
                            <img src="../<?= $category['categories_image_path'] ?>" class="card-img-top" alt="<?= htmlspecialchars($category['name']) ?>" style="height: 200px; object-fit: cover;">
                        <?php else: ?>
                            <div class="card-img-top bg-light text-center" style="height: 200px; display: flex; justify-content: center; align-items: center;">
                                <span class="text-muted">No Image</span>
                            </div>
                        <?php endif; ?>
                        
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($category['name']) ?></h5>
                            <p class="card-text">
                                <?php if (!empty($category['categories_image_path'])): ?>
                                    <small class="text-muted">Image available</small>
                                <?php else: ?>
                                    <small class="text-muted">No image uploaded</small>
                                <?php endif; ?>
                            </p>
                            <div class="d-flex justify-content-between">
                                <a href="./edit_category.php?id=<?= $category['category_id'] ?>" class="btn btn-warning btn-lg">Edit</a>
                                <a href="./delete_category.php?id=<?= $category['category_id'] ?>" class="btn btn-danger btn-lg" onclick="return confirm('Are you sure you want to delete this category?');">Delete</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center text-muted w-100">No categories available. Click "Add New Category" to create one.</p>
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
