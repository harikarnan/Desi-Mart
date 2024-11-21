<?php
session_start();
?>
<header>
    <h1>Welcome to DesiMart</h1>
    <nav>
        <a href="index.php">Home</a>
        <a href="products.php">Products</a>
        <a href="cart.php">Cart</a>
        <a href="checkout.php">Checkout</a>
        <?php if (isset($_SESSION['user'])): ?>
            <a href="logout.php">Logout</a>
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['user']['name']); ?>!</span>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        <?php endif; ?>
    </nav>
</header>
