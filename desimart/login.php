<?php
require_once 'db.php';
require_once 'classes/User.php';
session_start();

$db = (new Database())->getConnection();
$userObj = new User($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']);

    $user = $userObj->login($email, $password);

    if ($user) {
        $_SESSION['user'] = $user;
        header('Location: index.php');
    } else {
        $error = "Invalid email or password.";
    }
}
?>
<?php include 'includes/header.php'; ?>
<h1>Login</h1>
<?php if (isset($error)): ?>
    <p><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>
<form method="POST">
    <label for="email">Email:</label>
    <input type="email" name="email" id="email" required>
    <label for="password">Password:</label>
    <input type="password" name="password" id="password" required>
    <button type="submit">Login</button>
</form>
<?php include 'includes/footer.php'; ?>
