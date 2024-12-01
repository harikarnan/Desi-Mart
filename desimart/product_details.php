<?php
require_once 'db.php';

$db = (new Database())->getConnection();
$product_id = $_GET['id'] ?? 0;

// Fetch product details
$query = "SELECT product_id, name, description, price, products_image_path FROM products WHERE product_id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $product_id, PDO::PARAM_INT);
$stmt->execute();
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die("Product not found.");
}
?>

<?php include 'includes/header.php'; ?>
<main>
    <h1><?php echo htmlspecialchars($product['name']); ?></h1>
    <img src="<?php echo htmlspecialchars($product['products_image_path']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
    <p><?php echo htmlspecialchars($product['description']); ?></p>
    <p>Price: $<?php echo htmlspecialchars(number_format($product['price'], 2)); ?></p>
    <a href="add_to_cart.php?id=<?php echo htmlspecialchars($product['product_id']); ?>" class="primary-btn">Add to Cart</a>
</main>
<?php include 'includes/footer.php'; ?>