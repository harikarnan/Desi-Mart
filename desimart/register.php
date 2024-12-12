<?php
session_start();
require_once 'db.php';

$db = (new Database())->getConnection();

// Initialize errors array
$errors = [];
$valid = true;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']);
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT); // Securely hash the password

    // Name validation - Only alphabets (letters)
    if (empty($name)) {
        $errors['name'] = "Name is required.";
        $valid = false;
    } elseif (strlen($name) > 50) {
        $errors['name'] = "Name cannot exceed 50 characters.";
        $valid = false;
    } elseif (!preg_match("/^[a-zA-Z]+$/", $name)) {
        $errors['name'] = "Name must contain only alphabetic characters.";
        $valid = false;
    }

    // Email validation
    if (empty($email)) {
        $errors['email'] = "Email is required.";
        $valid = false;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format.";
        $valid = false;
    } else {
        // Check if email already exists
        $query = "SELECT email FROM users WHERE email = :email";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            $errors['email'] = "Email is already registered.";
            $valid = false;
        }
    }

    // Password validation
    if (empty($password)) {
        $errors['password'] = "Password is required.";
        $valid = false;
    } elseif (strlen($password) < 8) {
        $errors['password'] = "Password must be at least 8 characters long.";
        $valid = false;
    } elseif (!preg_match("/[A-Z]/", $password)) {
        $errors['password'] = "Password must include at least one uppercase letter.";
        $valid = false;
    } elseif (!preg_match("/[a-z]/", $password)) {
        $errors['password'] = "Password must include at least one lowercase letter.";
        $valid = false;
    } elseif (!preg_match("/[0-9]/", $password)) {
        $errors['password'] = "Password must include at least one number.";
        $valid = false;
    } elseif (!preg_match("/[\W_]/", $password)) {
        $errors['password'] = "Password must include at least one special character.";
        $valid = false;
    }

    // If the form is valid, proceed with registration
    if ($valid) {
        // Insert new user into the database
        $query = "INSERT INTO users (name, email, password) VALUES (:name, :email, :password)";
        $stmt = $db->prepare($query);
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':password' => $hashedPassword
        ]);

        // Automatically log in the user after registration
        // $userId = $db->lastInsertId();
        // $_SESSION['user'] = [
        //     'id' => $userId,
        //     'name' => $name,
        //     'email' => $email
        // ];

        header('Location: login.php'); // Redirect to homepage
        exit();
    }
}
?>

<?php include 'includes/header.php'; ?>
<main>
    <div class="common-container">
        <h1>Welcome to DesiMart</h1>
        <h2>Register</h2>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <strong>Please fix the following errors:</strong>
                <ul style="list-style-type: none; padding-left: 0;">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" name="name" id="name" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($name ?? ''); ?>" required maxlength="50" pattern="[A-Za-z]+" title="Name must contain only alphabetic characters.">
                <?php if (isset($errors['name'])): ?>
                    <div class="invalid-feedback"><?php echo $errors['name']; ?></div>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                <?php if (isset($errors['email'])): ?>
                    <div class="invalid-feedback"><?php echo $errors['email']; ?></div>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" required>
                <?php if (isset($errors['password'])): ?>
                    <div class="invalid-feedback"><?php echo $errors['password']; ?></div>
                <?php endif; ?>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a>.</p>
    </div>
</main>
<?php include 'includes/footer.php'; ?>
