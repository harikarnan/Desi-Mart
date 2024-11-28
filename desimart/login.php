<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']);
    
    $db = (new Database())->getConnection();
    $query = "SELECT * FROM users WHERE email = :email";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email']
        ];
        header('Location: index.php'); // Redirect to the homepage
        exit();
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DesiMart - Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <main>
        <div class="common-container">
            <h1>Welcome to DesiMart</h1>
            <h2>Login</h2>
            <?php if (isset($error)): ?>
                <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <form method="POST">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" name="email" id="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" name="password" id="password" required>
                </div>
                <button type="submit" class="primary-btn">Login</button>
            </form>
            <p>Don't have an account? <a href="register.php">Register here</a>.</p>
        </div>
    </main>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
