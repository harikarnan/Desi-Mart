<?php
$order_id = $_GET['order_id'] ?? null;
if (!$order_id) {
    die("Invalid order ID.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Placed Successfully</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
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
</body>
</html>
