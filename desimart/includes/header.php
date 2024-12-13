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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="styles/styles.css">

</head>

<body>
    <header>
        <nav>
            <div class="d-flex align-items-center">
                <a class="bg-none" href="index.php"><img class="logo" src="images/logo.jpeg" alt="Company logo" /></a>
                <h1 class="ms-2">Desi Mart</h1>
            </div>
            <div>
                <?php if (isset($_SESSION['user'])): ?>
                    <a class="menu" href="index.php">Home</a>
                    <a class="menu"  href="products.php">Products</a>
                    <a class="menu"  href="cart.php">Cart</a>
                    <a  class="menu" href="logout.php?confirm=true">Logout</a>
                    <span>Welcome, <?php echo ucwords(htmlspecialchars($_SESSION['user']['name'])); ?>!</span>
                <?php endif; ?>
            </div>
        </nav>
    </header>