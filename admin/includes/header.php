<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin'])) {
    header('Location: ../admin_login.php');
    exit();
}
?>
<header>
    <nav>
        <ul>
            <li><a href="/php-team/admin/index.php">Dashboard</a></li>
            <li><a href="/php-team/admin/categories/index.php">Manage Categories</a></li>
            <li><a href="/php-team/admin/products/index.php">Manage Products</a></li>
            <li><a href="/php-team/admin/admin_logout.php">Logout</a></li>
        </ul>
    </nav>
</header>
<link rel="stylesheet" href="/php-team/admin/styles.css">
