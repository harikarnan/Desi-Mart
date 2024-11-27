<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<header>
    <h1>Welcome to DesiMart</h1>
    <nav>
        <?php if (isset($_SESSION['user'])): ?>
            <a href="index.php">Home</a>
            <a href="products.php">Products</a>
            <a href="cart.php">Cart</a>
            <a href="checkout.php">Checkout</a>
            <a href="logout.php">Logout</a>
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['user']['name']); ?>!</span>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        <?php endif; ?>
    </nav>
</header>
