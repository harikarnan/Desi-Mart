<?php
session_start();
session_unset();
session_destroy();
header('Location: login.php');
exit();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DesiMart - Logout</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <main>
        <div class="common-container">
            <h1>You have been logged out</h1>
            <p>Thank you for visiting DesiMart! You have successfully logged out.</p>
            <p><a href="login.php">Login again</a></p>
        </div>
    </main>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
