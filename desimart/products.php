<?php
include 'db.php';
$db = (new Database())->getConnection();

// Fetch categories
$query = "SELECT * FROM categories";
$stmt = $db->prepare($query);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch products
$categoryFilter = $_GET['category'] ?? null;
$query = "SELECT * FROM products";
if ($categoryFilter) {
    $query .= " WHERE category_id = :category_id";
}
$stmt = $db->prepare($query);
if ($categoryFilter) {
    $stmt->bindParam(':category_id', $categoryFilter, PDO::PARAM_INT);
}
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DesiMart - Products</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Products</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="cart.php">Cart</a>
            <a href="checkout.php">Checkout</a>
        </nav>
    </header>
    <main>
        <form method="GET">
            <label for="category">Filter by Category:</label>
            <select name="category" id="category" onchange="this.form.submit()">
                <option value="">All</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['id']; ?>" <?php echo ($category['id'] == $categoryFilter) ? 'selected' : ''; ?>>
                        <?php echo $category['name']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
        <div class="product-list">
            <?php foreach ($products as $product): ?>
                <div class="product-item">
                    <img src="images/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                    <h3><?php echo $product['name']; ?></h3>
                    <p>$<?php echo $product['price']; ?></p>
                    <a href="product_details.php?id=<?php echo $product['id']; ?>">View Details</a>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
</body>
</html>
