<?php
session_start();

// Redirect to login if the user is not logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

require_once 'classes/States.php';
require 'classes/Sanitizer.php';

$sanitize_input = new Sanitizer();

$states = new States('data/states.json');
$statesArray = $states->getStates();

if (isset($statesArray['error'])) {
    http_response_code(400);
    die($statesArray['error']);
}

$cart = $_SESSION['cart'] ?? [];
$totalAmount = 0;
$taxRate = 0.13;
$totalPurchases = 0;

// Get user data from session
$user_name = htmlspecialchars($_SESSION['user']['name']);
$user_email = htmlspecialchars($_SESSION['user']['email']);

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
    $name = $sanitize_input->sanitize_input($_POST['name']);
    $address = $sanitize_input->sanitize_input($_POST['address']);
    $email = $sanitize_input->sanitize_input($_POST['email']);
    $mobile_number = $sanitize_input->sanitize_input($_POST['mobile_number']);
    $city = $sanitize_input->sanitize_input($_POST['city']);
    $province = $sanitize_input->sanitize_input($_POST['province']);
    $country = $sanitize_input->sanitize_input($_POST['country']);
    $pincode = $sanitize_input->sanitize_input($_POST['pincode']);

    // Validate inputs
    if (empty($name)) {
        $errors['name'] = "Name is required.";
        $valid = false;
    } elseif (strlen($name) > 50) {
        $errors['name'] = "Name cannot exceed 50 characters.";
        $valid = false;
    } elseif (!preg_match("/^[a-zA-Z ]+$/", $name)) {
        $errors['name'] = "Name must contain only alphabetic characters.";
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

    // Validate mobile number server-side
    if (empty($mobile_number)) {
        $errors['mobile_number'] = "Mobile number is required.";
        $valid = false; // Set valid flag to false
    } elseif (!preg_match('/^\d{10}$/', $mobile_number)) {
        $errors['mobile_number'] = "Invalid mobile number. It must contain exactly 10 digits.";
        $valid = false; // Set valid flag to false
    }

    if (empty($address)) {
        $errors['address'] = "Address is required.";
        $valid = false;
    } elseif (strlen($address) > 100) {
        $errors['address'] = "Address cannot exceed 100 characters.";
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

    // Validate pincode for Canadian format (A1A 1A1)
    if (empty($pincode)) {
        $errors['pincode'] = "Pincode is required.";
        $valid = false;
    } elseif (!preg_match('/^[A-Z]\d[A-Z] \d[A-Z]\d$/', strtoupper($pincode))) {
        $errors['pincode'] = "Please provide a valid pincode in the format A1A 1A1. Only letters and digits are allowed.";
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
                
            <?php endif; ?>

            <!-- Left: Checkout Form -->
            <div class="col-md-8 d-flex align-items-center justify-content-center">
                <div class="card shadow-sm rounded border-0 w-100">
                    <div class="card-body d-flex flex-column" style="max-height: 100%; overflow-y: auto;">
                        <h2 class="text-center mb-4">Customer's Details</h2>
                        <form method="POST" class="d-flex flex-column justify-content-between p-4" style="flex-grow: 1; margin-bottom: 40px;">
                            <!-- Full Name -->
                            <div class="form-group mb-3">
                                <label for="name" class="mb-2">Full Name:</label>
                                <input type="text" name="name" id="name"
                                    class="form-control form-control-lg <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>"
                                    value="<?php echo htmlspecialchars($name ?? $user_name); ?>" maxlength="50"
                                    pattern="[A-Za-z ]+" title="Name must contain only alphabetic characters and be less than 50 characters.">
                                <?php if (isset($errors['name'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['name']; ?></div>
                                <?php endif; ?>
                            </div>

                             <!-- Email -->
                            <div class="form-group mb-3">
                                <label for="email" class="mb-2">Email:</label>
                                <input type="email" name="email" id="email"
                                class="form-control form-control-lg <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>"
                                value="<?php echo htmlspecialchars($email ?? $user_email); ?>">
                                <?php if (isset($errors['email'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['email']; ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Mobile Number -->
                            <div class="form-group mb-3">
                                <label for="mobile_number" class="mb-2">Mobile Number:</label>
                                <input type="text" name="mobile_number"
                                    id="mobile_number"
                                    class="form-control form-control-lg <?php echo isset($errors['mobile_number']) ? 'is-invalid' : ''; ?>"
                                    value="<?php echo htmlspecialchars($mobile_number ?? ''); ?>"
                                    maxlength="10" placeholder="Please enter a valid 10-digit number."
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                <?php if (isset($errors['mobile_number'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['mobile_number']; ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Address -->
                            <div class="form-group mb-3">
                                <label for="address" class="mb-2">Address Line 1:</label>
                                <textarea name="address" id="address" rows="4" class="form-control form-control-lg <?php echo isset($errors['address']) ? 'is-invalid' : ''; ?>" maxlength="100"><?php echo htmlspecialchars($address ?? ''); ?></textarea>
                                <?php if (isset($errors['address'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['address']; ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- City -->
                            <div class="form-group mb-3">
                                <label for="city" class="mb-2">City:</label>
                                <input type="text" name="city" id="city" class="form-control form-control-lg <?php echo isset($errors['city']) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($city ?? ''); ?>"  maxlength="50">
                                <?php if (isset($errors['city'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['city']; ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Province -->
                            <div class="form-group mb-3">
                                <label for="province" class="mb-2">Province:</label>
                                <select name="province" id="province" class="form-control form-control-lg <?php echo isset($errors['province']) ? 'is-invalid' : ''; ?>" >
                                    <option value="">Select Province</option>
                                    <?php
                                    foreach ($statesArray as $state) { 
                                        $selected = ($state === $province) ? "selected" : "";
                                        echo "<option value=\"" . htmlspecialchars($state) . "\" $selected>" . htmlspecialchars($state) . "</option>";
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
                                <input type="text" name="pincode"
                                    id="pincode"
                                    placeholder="K1A 0B1"
                                    class="form-control form-control-lg <?php echo isset($errors['pincode']) ? 'is-invalid' : ''; ?>"
                                    value="<?php echo htmlspecialchars($pincode ?? ''); ?>"
                                    pattern="^[A-Z]\d[A-Z] \d[A-Z]\d$"
                                    maxlength="7">
                                <?php if (isset($errors['pincode'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['pincode']; ?></div>
                                <?php endif; ?>
                            </div>

                            <button type="submit" class="primary-btn col-md-4 btn-lg mx-auto mt-3">Place Order</button>
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
                                <span>Total Products</span>
                                <span><?php echo $totalPurchases; ?></span> <!-- Display total products here -->
                            </li>
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
