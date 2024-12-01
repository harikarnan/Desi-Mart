<?php
session_start();
session_unset();
session_destroy();
header('Location: login.php');
exit();
?>


<?php include 'includes/header.php'; ?>
<main>
    <div class="common-container">
        <h1>You have been logged out</h1>
        <p>Thank you for visiting DesiMart! You have successfully logged out.</p>
        <p><a href="login.php">Login again</a></p>
    </div>
</main>
<?php include 'includes/footer.php'; ?>