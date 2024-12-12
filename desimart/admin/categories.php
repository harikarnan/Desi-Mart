<?php
require_once './includes/db.php'; // Database connection
require_once './includes/header.php'; // Header with navigation

// Fetch all categories
$stmt = $db->query("SELECT * FROM categories");
$categories = $stmt->fetch_all(MYSQLI_ASSOC);
?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Manage Categories</h2>

    <!-- Add Category Button -->
    <div class="text-end mb-3">
        <a href="./add_category.php" class="btn btn-success">Add New Category</a>
    </div>

    <!-- Categories Table -->
    <?php if (!empty($categories)): ?>
        <table class="table table-bordered table-striped">
            <thead class="table-primary">
                <tr>
                    <th>Category ID</th>
                    <th>Category Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td><?= $category['category_id'] ?></td>
                        <td><?= $category['name'] ?></td>
                        <td>
                            <a href="./edit_category.php?id=<?= $category['category_id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="./delete_category.php?id=<?= $category['category_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this category?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="text-center">No categories available. Click "Add New Category" to create one.</p>
    <?php endif; ?>
</div>

<?php require_once './includes/footer.php'; // Footer ?>
