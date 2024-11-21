<?php
require_once 'db.php';
require_once 'classes/Order.php';
require_once 'generate_invoice.php';
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$db = (new Database())->getConnection();
$order = new Order($db);

$cartItems = $_SESSION['cart'];
$totalAmount = array_reduce($cartItems, fn($sum, $item) => $sum + ($item['quantity'] * $item['price']), 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = $order->createOrder($_SESSION['user']['id'], $cartItems, $totalAmount);
    $invoicePath = generateInvoice($orderId, $_SESSION['user'], $cartItems, $totalAmount);

    unset($_SESSION['cart']); // Clear the cart after checkout
    echo "<p>Order placed successfully!</p>";
    echo "<a href='$invoicePath' target='_blank'>Download Invoice</a>";
    exit();
}
?>
<?php include 'includes/header.php'; ?>
<h1>Checkout</h1>
<p>Total Amount: $<?php echo number_format($totalAmount, 2); ?></p>
<form method="POST">
    <button type="submit">Place Order</button>
</form>
<?php include 'includes/footer.php'; ?>
