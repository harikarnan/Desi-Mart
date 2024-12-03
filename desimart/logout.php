<?php
session_start();
include 'includes/header.php'; 

if (isset($_GET['confirm']) && $_GET['confirm'] === 'true') {
?>

<main>
  <div class="common-container">
    <h1>Are you sure you want to log out?</h1>
    <a href="logout.php?action=logout" class="btn btn-danger 
        m-2">Log Out</a>
        <a href="index.php" class="btn btn-primary">Stay Logged In</a>
  </div>
</main>

<?php
  include 'includes/footer.php';
  exit();
}

if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    // Destroy session and logout
    session_unset();
    session_destroy();
    header('Location: logout.php'); // Redirect to logged out page
    exit();
}
?>

<main>
    <div class="common-container">
        <h1>Logout Successful!</h1>
        <p>Thank you for visiting DesiMart!</p>
        <p><a href="login.php" class="btn btn-primary">Login again</a></p>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
