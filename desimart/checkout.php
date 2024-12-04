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

// Initialize error array
$errors = [];
$valid = true;

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

    // Validate inputs
    if (empty($first_name)) {
        $errors['first_name'] = "First name is required.";
        $valid = false;
    } elseif (strlen($first_name) > 50) {
        $errors['first_name'] = "First name cannot exceed 50 characters.";
        $valid = false;
    } elseif (!preg_match("/^[a-zA-Z]+$/", $first_name)) {
        $errors['first_name'] = "First name must contain only alphabetic characters.";
        $valid = false;
    }

    if (empty($last_name)) {
        $errors['last_name'] = "Last name is required.";
        $valid = false;
    } elseif (strlen($last_name) > 50) {
        $errors['last_name'] = "Last name cannot exceed 50 characters.";
        $valid = false;
    } elseif (!preg_match("/^[a-zA-Z]+$/", $last_name)) {
        $errors['last_name'] = "Last name must contain only alphabetic characters.";
        $valid = false;
    }

    if (empty($address)) {
        $errors['address'] = "Address is required.";
        $valid = false;
    } elseif (strlen($address) > 100) {
        $errors['address'] = "Address cannot exceed 100 characters.";
        $valid = false;
    }

    if (empty($city)) {
        $errors['city'] = "City is required.";
        $valid = false;
    } elseif (strlen($city) > 50) {
        $errors['city'] = "City cannot exceed 50 characters.";
        $valid = false;
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Please provide a valid email address.";
        $valid = false;
    }

    if (empty($mobile_number) || !preg_match("/^[0-9]{10}$/", $mobile_number)) {
        $errors['mobile_number'] = "Please provide a valid 10-digit mobile number.";
        $valid = false;
    }

    if (empty($province)) {
        $errors['province'] = "Province is required.";
        $valid = false;
    }

    // Default country is Canada, no validation needed
    if (empty($country)) {
        $country = 'Canada'; // Default to Canada
    }

    if (empty($pincode) || !preg_match("/^[A-Za-z]\d[A-Za-z] \d[A-Za-z]\d$/", $pincode)) {
        $errors['pincode'] = "Please provide a valid pincode in the format A1A 1A1.";
        $valid = false;
    }

    // If no errors, proceed with checkout
    if ($valid) {
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
}
?>

<?php include 'includes/header.php'; ?>
<main>
    <div class="container-fluid d-flex">
        <div class="row w-100">
            <h1 class="my-4">Checkout</h1>

            <!-- Display validation errors as an alert box -->
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger col-md-8 mx-auto">
                    <strong>Please fix the following errors:</strong>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Left: Checkout Form -->
            <div class="col-md-8 d-flex align-items-center justify-content-center">
                <div class="card shadow-sm rounded border-0 w-100">
                    <div class="card-body d-flex flex-column" style="max-height: 100%; overflow-y: auto;">
                        <h2 class="text-center mb-4">Customer's Details</h2>
                        <form method="POST" class="d-flex flex-column justify-content-between p-4" style="flex-grow: 1; margin-bottom: 40px;">
                            <!-- First Name -->
                            <div class="form-group mb-3">
                                <label for="first_name" class="mb-2">First Name:</label>
                                <input type="text" name="first_name" id="first_name" class="form-control form-control-lg <?php echo isset($errors['first_name']) ? 'is-invalid' : (isset($first_name) ? 'is-valid' : ''); ?>" value="<?php echo htmlspecialchars($first_name ?? ''); ?>" required maxlength="50" pattern="[A-Za-z]+" title="First name must contain only alphabetic characters and be less than 50 characters.">
                                <?php if (isset($errors['first_name'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['first_name']; ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Last Name -->
                            <div class="form-group mb-3">
                                <label for="last_name" class="mb-2">Last Name:</label>
                                <input type="text" name="last_name" id="last_name" class="form-control form-control-lg <?php echo isset($errors['last_name']) ? 'is-invalid' : (isset($last_name) ? 'is-valid' : ''); ?>" value="<?php echo htmlspecialchars($last_name ?? ''); ?>" required maxlength="50" pattern="[A-Za-z]+" title="Last name must contain only alphabetic characters and be less than 50 characters.">
                                <?php if (isset($errors['last_name'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['last_name']; ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Address -->
                            <div class="form-group mb-3">
                                <label for="address" class="mb-2">Address:</label>
                                <textarea name="address" id="address" rows="4" class="form-control form-control-lg <?php echo isset($errors['address']) ? 'is-invalid' : (isset($address) ? 'is-valid' : ''); ?>" required maxlength="100"><?php echo htmlspecialchars($address ?? ''); ?></textarea>
                                <?php if (isset($errors['address'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['address']; ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Email -->
                            <div class="form-group mb-3">
                                <label for="email" class="mb-2">Email:</label>
                                <input type="email" name="email" id="email" class="form-control form-control-lg <?php echo isset($errors['email']) ? 'is-invalid' : (isset($email) ? 'is-valid' : ''); ?>" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                                <?php if (isset($errors['email'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['email']; ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Mobile Number -->
                            <div class="form-group mb-3">
                                <label for="mobile_number" class="mb-2">Mobile Number:</label>
                                <input type="text" name="mobile_number" id="mobile_number" class="form-control form-control-lg <?php echo isset($errors['mobile_number']) ? 'is-invalid' : (isset($mobile_number) ? 'is-valid' : ''); ?>" value="<?php echo htmlspecialchars($mobile_number ?? ''); ?>" required pattern="^\d{10}$" title="Please enter a valid 10-digit number.">
                                <?php if (isset($errors['mobile_number'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['mobile_number']; ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- City -->
                            <div class="form-group mb-3">
                                <label for="city" class="mb-2">City:</label>
                                <input type="text" name="city" id="city" class="form-control form-control-lg <?php echo isset($errors['city']) ? 'is-invalid' : (isset($city) ? 'is-valid' : ''); ?>" value="<?php echo htmlspecialchars($city ?? ''); ?>" required maxlength="50">
                                <?php if (isset($errors['city'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['city']; ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Province -->
                            <div class="form-group mb-3">
                                <label for="province" class="mb-2">Province:</label>
                                <select name="province" id="province" class="form-control form-control-lg <?php echo isset($errors['province']) ? 'is-invalid' : (isset($province) ? 'is-valid' : ''); ?>" value="<?php echo htmlspecialchars($province ?? ''); ?>" required>
                                <option value="">Select Province</option>
                                    <?php
                                    foreach ($statesArray as $state) {
                                        echo "<option value=\"" . htmlspecialchars($state) . "\">" . htmlspecialchars($state) . "</option>";
                                    }
                                    ?>
                                </select>
                                
                                <?php if (isset($errors['province'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['province']; ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Country (default to Canada) -->
                            <div class="form-group mb-3">
                                <label for="country" class="mb-2">Country:</label>
                                <input type="text" name="country" id="country" class="form-control form-control-lg" value="Canada" readonly>
                            </div>

                            <!-- Pincode -->
                            <div class="form-group mb-3">
                                <label for="pincode" class="mb-2">Pincode:</label>
                                <input type="text" name="pincode" id="pincode" placeholder="A1A 1A1" class="form-control form-control-lg <?php echo isset($errors['pincode']) ? 'is-invalid' : (isset($pincode) ? 'is-valid' : ''); ?>" value="<?php echo htmlspecialchars($pincode ?? ''); ?>" required pattern="^[A-Za-z]\d[A-Za-z] \d[A-Za-z]\d$" title="Pincode should be in the format A1A 1A1">
                                <?php if (isset($errors['pincode'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['pincode']; ?></div>
                                <?php endif; ?>
                            </div>

                            <button type="submit" class="btn btn-primary col-md-4 btn-lg mx-auto mt-3">Place Order</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Right: Order Summary -->
            <div class="col-md-4">
                <div class="card shadow-sm rounded border-0">
                    <div class="card-body">
                        <h2 class="text-center mb-4">Order Summary</h2>
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Total Amount</span>
                                <span><?php echo '$' . number_format($totalAmount, 2); ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Tax (<?php echo ($taxRate * 100) . '%'; ?>)</span>
                                <span><?php echo '$' . number_format($taxAmount, 2); ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <strong>Overall Total</strong>
                                <strong><?php echo '$' . number_format($overallTotal, 2); ?></strong>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php include 'includes/footer.php'; ?>