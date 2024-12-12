<?php
if (!isset($_SESSION)) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<header class="bg-primary text-white p-3">
    <div class="container">
        <h1 class="h3">Admin Panel</h1>
        <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin'): ?>
            <nav>
                <a href="./dashboard.php" class="btn btn-light btn-sm">Dashboard</a>
                <a href="./categories.php" class="btn btn-light btn-sm">Manage Categories</a>
                <a href="./products.php" class="btn btn-light btn-sm">Manage Products</a>
                <a href="./logout.php" class="btn btn-danger btn-sm">Logout</a>
            </nav>
        <?php endif; ?>
    </div>
</header>
