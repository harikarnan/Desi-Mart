<?php
session_start();

require 'classes/Sanitizer.php';

$sanitize_input = new Sanitizer();

// Retrieve cart from session
$cart = $_SESSION['cart'] ?? [];
$totalAmount = 0;

// Calculate the total amount
if (!empty($cart)) {
    foreach ($cart as $product_id => $item) {
        $price = isset($item['price']) ? (float)$item['price'] : 0;
        $quantity = isset($item['quantity']) ? (int)$item['quantity'] : 0;

        if ($price > 0 && $quantity > 0) {
            $totalAmount += $price * $quantity;
        }
    }
}

// Handle form submission for quantity update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $sanitize_input->sanitize_input($_POST['product_id']);
    $quantity = (int)$sanitize_input->sanitize_input($_POST['quantity']);

    // Ensure minimum quantity is 1
    $quantity = max(1, $quantity);

    // Check if the quantity exceeds the maximum allowed limit
    if ($quantity > 8) {
        $_SESSION['alert'] = "The maximum quantity allowed for a product is 8.";
        $quantity = 8; // Set quantity to the limit
    }

    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity'] = $quantity; // Update the quantity
    }

    header('Location: cart.php'); // Redirect to refresh the page
    exit();
}
?>

<?php include 'includes/header.php'; ?>
<main class="container mt-5">
    <h1 class="text-center mb-4">Your Cart</h1>

    <!-- Display Alert if Set -->
    <?php if (isset($_SESSION['alert'])): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($_SESSION['alert']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['alert']);?>
    <?php endif; ?>

    <?php if (empty($cart)): ?>
        <p class="text-center">Your cart is empty. <a href="index.php" class="btn btn-link">Start shopping</a>.</p>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($cart as $product_id => $item): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card shadow-sm border">
                        <div class="card-img-top d-flex justify-content-center align-items-center bg-light" style="height:250px; overflow: hidden;">
                            <img 
                                src="<?php echo htmlspecialchars($item['products_image_path'] ?: 'images/placeholder.png'); ?>" 
                                alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                class="img-fluid w-100 h-100 rounded" style="object-fit: cover;" >
                        </div>
                        <div class="card-body text-start">
                            <h5 class="card-title"><?php echo htmlspecialchars($item['name']); ?></h5>
                            <p class="card-text">Price: <strong>$<?php echo htmlspecialchars(number_format($item['price'], 2)); ?></strong></p>
                            
                            <form method="POST" class="mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <label for="quantity-<?php echo $product_id; ?>" class="form-label mb-0">Quantity:</label>
                                    <input 
                                        type="text" 
                                        name="quantity" 
                                        inputmode="numeric"
                                        id="quantity-<?php echo $product_id; ?>" 
                                        value="<?php echo htmlspecialchars($item['quantity']); ?>" 
                                        min="1" 
                                        max="8"
                                        maxlength="1"
                                        class="form-control flex-grow-1" 
                                        required>
                                    <button type="submit" class="btn btn-info btn-block">Update</button>
                                </div>
                                <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                            </form>
                 
                            <p class="card-text">Total: <strong>$<?php echo htmlspecialchars(number_format($item['price'] * $item['quantity'], 2)); ?></strong></p>
                            <a href="remove_from_cart.php?id=<?php echo htmlspecialchars($product_id); ?>" class="btn btn-danger btn-sm">Remove</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="mt-4 text-end border-top p-3">
            <p class="fw-bold fs-5">Grand Total: $<?php echo htmlspecialchars(number_format($totalAmount, 2)); ?></p>
            <a href="checkout.php" class="primary-btn">Proceed to Checkout</a>
        </div>
    <?php endif; ?>
</main>
<?php include 'includes/footer.php'; ?>
