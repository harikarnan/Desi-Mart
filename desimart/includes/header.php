<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DesiMart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">

</head>

<body>
    <header>
        <nav>
            <div>
                <img class="logo" src="images/logo.png" alt="Company logo " />
            </div>
            <div>
                <?php if (isset($_SESSION['user'])): ?>
                    <a href="index.php">Home</a>
                    <a href="products.php">Products</a>
                    <a href="cart.php">Cart</a>
                    <a href="logout.php?confirm=true">Logout</a>
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['user']['name']); ?>!</span>
                <?php endif; ?>
            </div>
        </nav>
    </header>