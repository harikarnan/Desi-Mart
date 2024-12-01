<?php
$order_id = $_GET['order_id'] ?? null;
if (!$order_id) {
    die("Invalid order ID.");
}
?>

<?php include 'includes/header.php'; ?>
<main class="container">
    <h1>Order Placed Successfully!</h1>
    <p>Your order has been placed successfully. Thank you for shopping with us!</p>
    <div class="actions">
        <a href="generate_invoice.php?order_id=<?php echo htmlspecialchars($order_id); ?>" target="_blank" class="btn">View Invoice</a>
        <a href="index.php" class="btn">Continue Shopping</a>
    </div>
</main>
<?php include 'includes/footer.php'; ?>