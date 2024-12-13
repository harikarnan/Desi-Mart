<?php
require_once 'includes/db.php';
require_once '../classes/Sanitizer.php';

$sanitize_input = new Sanitizer();
session_start();

if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin') {
    header("Location: dashboard.php");
    exit();
}

$error = [];
$valid = true;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $sanitize_input->sanitize_input($_POST['email']);
    $password = $sanitize_input->sanitize_input($_POST['password']);

    // Validate email format
    if (empty($email)) {
        $error['email'] = "Email is required.";
        $valid = false;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error['email'] = "Invalid email format.";
        $valid = false;
    } else {
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin'");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if (!$user) {
            $error['email'] = "No admin account found with this email.";
            $valid = false;
        }
    }

    // Validate password
    if (empty($password)) {
        $error['password'] = "Password is required.";
        $valid = false;
    }

    // If valid, check password with stored hash and log the admin in
    if ($valid && $user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];
        header('Location: dashboard.php');
        exit();
    } elseif ($valid) {
        $error['password'] = "Invalid email or password.";
    }
}
?>

<?php include 'includes/header.php'; ?>
<main>
    <div class="common-container">
        <h1>Welcome to DesiMart</h1>
        <h2>Admin Login</h2>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <strong>Please fix the following errors:</strong>
                <ul style="list-style-type: none; padding-left: 0;">
                    <?php foreach ($error as $err): ?>
                        <li><?php echo htmlspecialchars($err); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" class="form-control <?php echo isset($error['email']) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                <?php if (isset($error['email'])): ?>
                    <div class="invalid-feedback"><?php echo $error['email']; ?></div>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" class="form-control <?php echo isset($error['password']) ? 'is-invalid' : ''; ?>" required>
                <?php if (isset($error['password'])): ?>
                    <div class="invalid-feedback"><?php echo $error['password']; ?></div>
                <?php endif; ?>
            </div>
            <button type="submit" class="mx-auto primary-btn">Login</button>
        </form>
        <p class="pt-3">Don't have an account? <a href="register.php">Register here</a>.</p>
    </div>
</main>
<?php include 'includes/footer.php'; ?>
