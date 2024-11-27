<?php
require_once 'db.php';
require_once 'classes/User.php';
session_start();

$db = (new Database())->getConnection();
$userObj = new User($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']);

    // Register the user
    $userObj->register([
        'name' => $name,
        'email' => $email,
        'password' => $password
    ]);

    $_SESSION['message'] = "Registration successful. Please log in.";
    header('Location: login.php'); // Redirect to login page after registration
}
?>
<?php include 'includes/header.php'; ?>
<h1>Register</h1>
<form method="POST">
    <label for="name">Name:</label>
    <input type="text" name="name" id="name" required>
    
    <label for="email">Email:</label>
    <input type="email" name="email" id="email" required>
    
    <label for="password">Password:</label>
    <input type="password" name="password" id="password" required>
    
    <button type="submit">Register</button>
</form>
<?php include 'includes/footer.php'; ?>
