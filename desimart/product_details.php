<?php
require_once 'db.php';
require_once 'classes/Product.php';

$db = (new Database())->getConnection();
$productObj = new Product($db);

// Fetch product details
$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = $productObj->getProductById($productId);

if (!$product) {
    die("Product not found.");
}
?>
<?php include 'includes/header.php'; ?>
<h1><?php echo htmlspecialchars($product['name']); ?></h1>
<img src="images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
<p>Price: $<?php echo htmlspecialchars(number_format($product['price'], 2)); ?></p>
<p><?php echo htmlspecialchars($product['description']); ?></p>
<form action="cart.php" method="POST">
    <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
    <input type="hidden" name="name" value="<?php echo htmlspecialchars($product['name']); ?>">
    <input type="hidden" name="price" value="<?php echo htmlspecialchars($product['price']); ?>">
    <label for="quantity">Quantity:</label>
    <input type="number" name="quantity" id="quantity" value="1" min="1">
    <button type="submit">Add to Cart</button>
</form>
<?php include 'includes/footer.php'; ?>
