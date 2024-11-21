<?php
session_start();
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product = [
        'id' => (int)$_POST['id'],
        'name' => htmlspecialchars($_POST['name']),
        'quantity' => (int)$_POST['quantity'],
        'price' => (float)$_POST['price']
    ];

    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] === $product['id']) {
            $item['quantity'] += $product['quantity'];
            $found = true;
            break;
        }
    }

    if (!$found) {
        $_SESSION['cart'][] = $product;
    }
}

// Remove from cart
if (isset($_GET['action']) && $_GET['action'] === 'remove') {
    $idToRemove = (int)$_GET['id'];
    foreach ($_SESSION['cart'] as $index => $item) {
        if ($item['id'] === $idToRemove) {
            unset($_SESSION['cart'][$index]);
            break;
        }
    }
    $_SESSION['cart'] = array_values($_SESSION['cart']); // Re-index
}

$cartItems = $_SESSION['cart'];
?>
<?php include 'includes/header.php'; ?>
<h1>Your Cart</h1>
<?php if (empty($cartItems)): ?>
    <p>Your cart is empty.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cartItems as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td>$<?php echo number_format($item['price'], 2); ?></td>
                    <td>$<?php echo number_format($item['quantity'] * $item['price'], 2); ?></td>
                    <td><a href="cart.php?action=remove&id=<?php echo $item['id']; ?>">Remove</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <p>Total: $<?php echo number_format(array_reduce($cartItems, fn($sum, $item) => $sum + ($item['price'] * $item['quantity']), 0), 2); ?></p>
    <a href="checkout.php">Proceed to Checkout</a>
<?php endif; ?>
<?php include 'includes/footer.php'; ?>
