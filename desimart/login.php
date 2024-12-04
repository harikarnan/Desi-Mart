<?php
session_start();
require_once 'db.php';

// Initialize error messages
$error = [];
$valid = true;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']);

    // Validate email format
    if (empty($email)) {
        $error['email'] = "Email is required.";
        $valid = false;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error['email'] = "Invalid email format.";
        $valid = false;
    } else {
        // Check if email exists in the database
        $db = (new Database())->getConnection();
        $query = "SELECT * FROM users WHERE email = :email";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $error['email'] = "No account found with this email.";
            $valid = false;
        }
    }

    // Validate password
    if (empty($password)) {
        $error['password'] = "Password is required.";
        $valid = false;
    } elseif (strlen($password) < 8) {
        $error['password'] = "Password must be at least 8 characters.";
        $valid = false;
    } elseif (!preg_match("/[A-Z]/", $password)) {
        $error['password'] = "Password must contain at least one uppercase letter.";
        $valid = false;
    } elseif (!preg_match("/[0-9]/", $password)) {
        $error['password'] = "Password must contain at least one number.";
        $valid = false;
    } elseif (!preg_match("/[\W_]/", $password)) {
        $error['password'] = "Password must contain at least one special character.";
        $valid = false;
    }

    // If valid, check password with stored hash and log the user in
    if ($valid && $user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email']
        ];
        header('Location: index.php'); // Redirect to the homepage
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
        <h2>Login</h2>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <strong>Please fix the following errors:</strong>
                <ul>
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
            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a>.</p>
    </div>
</main>
<?php include 'includes/footer.php'; ?>
