<?php
session_start();

// Retrieve cart from session
$cart = $_SESSION['cart'] ?? [];
$totalAmount = 0;

// Calculate the total amount
foreach ($cart as $item) {
    $totalAmount += $item['price'] * $item['quantity'];
}

// Handle form submission for quantity update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = htmlspecialchars($_POST['product_id']);
    $quantity = max(1, (int) $_POST['quantity']); // Ensure minimum quantity is 1

    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity'] = $quantity; // Update the quantity
    }

    header('Location: cart.php'); // Redirect to refresh the page
    exit();
}
?>

<?php include 'includes/header.php'; ?>
<main>
    <h1>Your Cart</h1>
    <?php if (empty($cart)): ?>
        <p>Your cart is empty. <a href="index.php">Start shopping</a>.</p>
    <?php else: ?>
        <div class="cart-list">
            <?php foreach ($cart as $product_id => $item): ?>
                <div class="cart-item">
                    <img src="<?php echo htmlspecialchars($item['image_path'] ?: 'images/placeholder.png'); ?>"
                        alt="<?php echo htmlspecialchars($item['name']); ?>" style="width:100px; height:auto;">
                    <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                    <p>Price: $<?php echo htmlspecialchars(number_format($item['price'], 2)); ?></p>
                    <form method="POST" style="display: inline;">
                        <label for="quantity-<?php echo $product_id; ?>">Quantity:</label>
                        <input type="number" name="quantity" id="quantity-<?php echo $product_id; ?>"
                            value="<?php echo htmlspecialchars($item['quantity']); ?>" min="1" style="width: 60px;">
                        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                        <button type="submit" class="btn">Update</button>
                    </form>
                    <p>Total: $<?php echo htmlspecialchars(number_format($item['price'] * $item['quantity'], 2)); ?></p>
                    <a href="remove_from_cart.php?id=<?php echo htmlspecialchars($product_id); ?>" class="btn">Remove</a>
                </div>
            <?php endforeach; ?>
        </div>
        <p><strong>Grand Total: $<?php echo htmlspecialchars(number_format($totalAmount, 2)); ?></strong></p>
        <a href="checkout.php" class="btn">Proceed to Checkout</a>
    <?php endif; ?>
</main>
<?php include 'includes/footer.php'; ?>