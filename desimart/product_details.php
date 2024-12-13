<?php
session_start();


// Redirect to login page if the user is not logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

require_once 'db.php';
require_once 'classes/Product.php';
require 'classes/Sanitizer.php';


$db = (new Database())->getConnection();
$product_id = $_GET['id'] ?? 0;
$products = new Product($db);

$sanitize_input = new Sanitizer();

// Fetch product details
$query = "SELECT product_id, name, description, price, products_image_path FROM products WHERE product_id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $product_id, PDO::PARAM_INT);
$stmt->execute();
$product = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch featured products
$featuredProducts = $products->getAllProducts(4);


if (!$product) {
    die("Product not found.");
}


// Initialize the cart if not already set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle form submission to add/update cart
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quantity = isset($_POST['quantity']) ? (int)$sanitize_input->sanitize_input($_POST['quantity']) : 1;
    $quantity = max(1, min(8, $quantity)); // Ensure quantity is between 1 and 8

    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity'] = $quantity; // Update the quantity
    } else {
        $_SESSION['cart'][$product_id] = [
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => $quantity,
            'products_image_path' => $product['products_image_path']
        ];
    }

    $_SESSION['toast'] = "Product added to cart successfully!";
}


// Fetch current quantity in the cart
$current_quantity = $_SESSION['cart'][$product_id]['quantity'] ?? 0;

?>

<?php include 'includes/header.php'; ?>
<main>
    <section class="container">

    <?php if (isset($_SESSION['toast'])): ?>
            <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 11;">
                <div class="toast show bg-success text-white" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header">
                        <strong class="me-auto">Added!!!</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close" onclick="this.parentElement.parentElement.style.display='none';"></button>
                    </div>
                    <div class="toast-body">
                        <?php 
                            echo $_SESSION['toast']; 
                            unset($_SESSION['toast']); 
                        ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="row align-items-center">
            <div class="col-md-3">
                <img class="rounded w-100 rem-12" src="<?php echo htmlspecialchars($product['products_image_path']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
            </div>
            <div class="col-md-8">
                <h2><?php echo htmlspecialchars($product['name']); ?></h2>
                <p class="pt-3"><?php echo htmlspecialchars($product['description']); ?></p>
                <p>Price: $<?php echo htmlspecialchars(number_format($product['price'], 2)); ?></p>
                <form method="POST" class="mb-2">
                <div class="d-flex align-items-center gap-2">
                    <label for="quantity" class="form-label mb-0">Quantity:</label>
                    <input type="text" inputmode="numeric" id="quantity" name="quantity" class="form-control w-50" value="<?php echo htmlspecialchars($current_quantity > 0 ? $current_quantity : 1); ?>" min="1" max="8" maxlength="1">
                    <button type="submit" class="btn btn-info btn-block">Add to Cart</button>
                    </div>
                </form>
                <a href="cart.php" class="primary-btn mt-3">Go to Cart</a>
            </div>
        </div>
    </section>

    <!-- Featured Offers Section -->
    <section class="featured-offers container mt-5">
        <h2 class="text-center mb-4">Feature Products: </h2>
        <div class="row gy-4">
            <?php if (empty($featuredProducts)): ?>
                <p class="text-center">No products available at the moment.</p>
            <?php else: ?>
                <?php foreach ($featuredProducts as $product): ?>
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                        <div class="card h-100">
                            <img src="<?php echo htmlspecialchars($product['products_image_path'] ?: 'images/placeholder.png'); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>" style="height: 150px; object-fit: cover;">
                            <div class="card-body text-center">
                                <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                <p class="card-text text-muted">$<?php echo htmlspecialchars(number_format($product['price'], 2)); ?></p>
                                <a href="product_details.php?id=<?php echo htmlspecialchars($product['product_id']); ?>" class="btn-sm primary-btn ">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
</main>
<?php include 'includes/footer.php'; ?>