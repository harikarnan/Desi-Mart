<?php
session_start();

// Redirect to login if the user is not logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$cart = $_SESSION['cart'] ?? [];
$totalAmount = 0;

if (!empty($cart)) {
    foreach ($cart as $item) {
        $totalAmount += $item['price'] * $item['quantity'];
    }
}

// Handle form submission for checkout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize user inputs
    $first_name = htmlspecialchars($_POST['first_name']);
    $last_name = htmlspecialchars($_POST['last_name']);
    $address = htmlspecialchars($_POST['address']);
    $email = htmlspecialchars($_POST['email']);
    $mobile_number = htmlspecialchars($_POST['mobile_number']);
    $city = htmlspecialchars($_POST['city']);
    $province = htmlspecialchars($_POST['province']);
    $country = htmlspecialchars($_POST['country']);
    $pincode = htmlspecialchars($_POST['pincode']);

    require_once 'db.php';
    $db = (new Database())->getConnection();

    // Insert order into the database
    $query = "INSERT INTO orders (user_id, order_date, total_amount, address, email, mobile_number, city, province, country, pincode) 
              VALUES (:user_id, NOW(), :total_amount, :address, :email, :mobile_number, :city, :province, :country, :pincode)";
    $stmt = $db->prepare($query);
    $stmt->execute([ 
        ':user_id' => $_SESSION['user']['id'],
        ':total_amount' => $totalAmount,
        ':address' => $address,
        ':email' => $email,
        ':mobile_number' => $mobile_number,
        ':city' => $city,
        ':province' => $province,
        ':country' => $country,
        ':pincode' => $pincode
    ]);
    $order_id = $db->lastInsertId();

    // Insert order items
    foreach ($cart as $product_id => $item) {
        $query = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                  VALUES (:order_id, :product_id, :quantity, :price)";
        $stmt = $db->prepare($query);
        $stmt->execute([
            ':order_id' => $order_id,
            ':product_id' => $product_id,
            ':quantity' => $item['quantity'],
            ':price' => $item['price']
        ]);
    }

    // Clear the cart after successful checkout
    unset($_SESSION['cart']);

    // Redirect to success page
    header("Location: success.php?order_id=$order_id");
    exit();
}
?>
   <?php include 'includes/header.php'; ?>
<main>
    <div class="common-container">
        <h1>DesiMart</h1>
        <h2>Checkout</h2>
        <?php if (empty($cart)): ?>
            <p>Your cart is empty. <a href="index.php">Start shopping</a>.</p>
        <?php else: ?>
            <p>Total Amount: <strong>$<?php echo htmlspecialchars(number_format($totalAmount, 2)); ?></strong></p>
            <form method="POST">
                <div class="form-group">
                    <label for="first_name">First Name:</label>
                    <input type="text" name="first_name" id="first_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name:</label>
                    <input type="text" name="last_name" id="last_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="address">Address:</label>
                    <textarea name="address" id="address" class="form-control" required></textarea>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="mobile_number">Mobile Number:</label>
                    <input type="tel" name="mobile_number" id="mobile_number" class="form-control" pattern="[0-9]{10}" required>
                </div>
                <div class="form-group">
                    <label for="country">Country:</label>
                    <select name="country" id="country" class="form-control" required >
                        <option value="">Select Country</option>
                        <option value="Canada" disabled>Canada</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="province">Province:</label>
                    <select name="province" id="province" class="form-control" required>
                        <option value="">Select Province</option>
                        <option value="Alberta">Alberta</option>
                        <option value="British Columbia">British Columbia</option>
                        <option value="Manitoba">Manitoba</option>
                        <option value="New Brunswick">New Brunswick</option>
                        <option value="Newfoundland and Labrador">Newfoundland and Labrador</option>
                        <option value="Nova Scotia">Nova Scotia</option>
                        <option value="Ontario">Ontario</option>
                        <option value="Prince Edward Island">Prince Edward Island</option>
                        <option value="Quebec">Quebec</option>
                        <option value="Saskatchewan">Saskatchewan</option>
                        <option value="Northwest Territories">Northwest Territories</option>
                        <option value="Nunavut">Nunavut</option>
                        <option value="Yukon">Yukon</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="city">City:</label>
                    <input type="text" name="city" id="city" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="pincode">Pincode:</label>
                    <input type="text" name="pincode" id="pincode" class="form-control" pattern="[A-Za-z]\d[A-Za-z] ?\d[A-Za-z]\d" placeholder="E.g., A1A 1A1" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Place Order</button>
            </form>
        <?php endif; ?>
    </div>
</main>
<?php include 'includes/footer.php'; ?>
