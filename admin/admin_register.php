<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $db = (new Database())->getConnection();

    $query = "SELECT email FROM admin_users WHERE email = :email";
    $stmt = $db->prepare($query);
    $stmt->execute([':email' => $email]);

    if ($stmt->rowCount() > 0) {
        $error = "Email already registered.";
    } else {
        $query = "INSERT INTO admin_users (name, email, password) VALUES (:name, :email, :password)";
        $stmt = $db->prepare($query);
        $stmt->execute([':name' => $name, ':email' => $email, ':password' => $password]);
        $_SESSION['success'] = "Admin account created successfully!";
        header('Location: admin_login.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Register</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Admin Register</h1>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <form method="POST">
        <label for="name">Name:</label>
        <input type="text" name="name" id="name" required>

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>

        <button type="submit">Register</button>
    </form>
    <p>Already have an account? <a href="admin_login.php">Login here</a>.</p>
</body>
</html>
