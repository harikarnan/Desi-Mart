<?php
session_start();

// Redirect to login if the user is not logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

require_once 'classes/States.php';

$states = new States('data/states.json');

$statesArray = $states->getStates();

if (isset($statesList['error'])) {
    http_response_code(400);
    die($statesList['error']);
}

echo json_encode($_SESSION['user']);

$cart = $_SESSION['cart'] ?? [];
$totalAmount = 0;
$taxRate = 0.13; // Example tax rate (13%)
$totalPurchases = 0;

if (!empty($cart)) {
    foreach ($cart as $item) {
        $totalPurchases += $item['quantity'];
        $totalAmount += $item['price'] * $item['quantity'];
    }
}

$taxAmount = $totalAmount * $taxRate;
$overallTotal = $totalAmount + $taxAmount;

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
        ':total_amount' => $overallTotal,
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
    <div class="container-fluid d-flex ">
        <div class="row w-100">
            <h1 class="my-4">Checkout</h1>
            <!-- Left: Checkout Form -->
            <div class="col-md-8 d-flex align-items-center justify-content-center">
                <div class="card shadow-sm rounded border-0 w-100">
                    <div class="card-body d-flex flex-column" style="max-height: 100%; overflow-y: auto;">
                        <h2 class="text-center mb-4">Customer's Details</h2>
                        <form method="POST" class="d-flex flex-column justify-content-between p-4" style="flex-grow: 1; margin-bottom: 40px;">
                            <div class="form-group col-md-8 mb-3">
                                <label class="mb-2" for="first_name">First Name:</label>
                                <input type="text" name="first_name" id="first_name" class="form-control form-control-lg" required>
                            </div>
                            <div class="form-group col-md-8 mb-4">
                                <label class="mb-2" for="last_name">Last Name:</label>
                                <input type="text" name="last_name" id="last_name" class="form-control form-control-lg" required>
                            </div>
                            <div class="form-group col-md-8 mb-3">
                                <label class="mb-2" for="address">Address:</label>
                                <textarea name="address" id="address" rows="4" class="form-control form-control-lg" required></textarea>
                            </div>
                            <div class="form-group col-md-8 mb-3">
                                <label class="mb-2" for="email">Email:</label>
                                <input type="email" name="email" id="email" class="form-control form-control-lg" required>
                            </div>
                            <div class="form-group col-md-8 mb-3">
                                <label class="mb-2" for="mobile_number">Mobile Number:</label>
                                <input type="tel" name="mobile_number" id="mobile_number" class="form-control form-control-lg" pattern="[0-9]{10}" required>
                            </div>
                            <div class="form-group col-md-8 mb-3">
                                <label class="mb-2" for="country">Country:</label>
                                <select name="country" id="country" class="form-control form-control-lg" aria-readonly="true" required>
                                    <option value="">Select Country</option>
                                    <option value="Canada" selected>Canada</option>
                                </select>
                            </div>
                            <div class="form-group col-md-8 mb-3">
                                <label class="mb-2" for="province">Province:</label>
                                <select name="province" id="province" class="form-control form-control-lg" required>
                                    <option value="">Select Province</option>
                                    <?php
                                    foreach ($statesArray as $state) {
                                        echo "<option value=\"" . htmlspecialchars($state) . "\">" . htmlspecialchars($state) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group col-md-8 mb-3">
                                <label class="mb-2" for="city">City:</label>
                                <input type="text" name="city" id="city" class="form-control form-control-lg" required>
                            </div>
                            <div class="form-group col-md-8 mb-3">
                                <label class="mb-2" for="pincode">Pincode:</label>
                                <input type="text" name="pincode" id="pincode" placeholder="A1A 1A1" class="form-control form-control-lg" pattern="[A-Za-z]\d[A-Za-z] ?\d[A-Za-z]\d" required>
                            </div>
                            <button type="submit" class="btn btn-primary col-md-8 btn-lg btn-block mt-3" style="margin-top: auto;">Place Order</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Right: Order Summary -->
            <div class="col-md-4 d-flex align-items-start justify-content-center">
                <div class="card shadow-sm rounded border-0 w-100">
                    <div class="card-body">
                        <h2 class="text-center mb-4">Order Summary</h2>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Total Products:
                                <span class="badge badge-secondary"><?php echo $totalPurchases; ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Subtotal:
                                <span class="font-weight-bold">$<?php echo number_format($totalAmount, 2); ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Tax (13%):
                                <span class="font-weight-bold">$<?php echo number_format($taxAmount, 2); ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <strong>Overall Total:</strong>
                                <span class="font-weight-bold text-success">$<?php echo number_format($overallTotal, 2); ?></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>


<?php include 'includes/footer.php'; ?>