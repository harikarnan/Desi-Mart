<?php
require_once 'admin_auth.php';

include 'includes/header.php'; // Include header for consistency

// Show the confirmation page
if (isset($_GET['confirm']) && $_GET['confirm'] === 'true') {
    ?>
    <main>
        <div class="common-container text-center">
            <h1>Are you sure you want to log out?</h1>
            <!-- "Log Out" button destroys the session and redirects to the login page -->
            <a href="logout.php?action=logout" class="btn btn-danger m-2">Log Out</a>
            <!-- "Stay Logged In" button redirects back to the dashboard -->
            <a href="dashboard.php" class="btn btn-primary">Stay Logged In</a>
        </div>
    </main>
    <?php
    include 'includes/footer.php'; // Include footer
    exit();
}

// Handle the logout action
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    // Destroy the session and redirect to login page
    session_unset(); // Clear all session variables
    session_destroy(); // Destroy the session
    header("Location: login.php"); // Redirect to login
    exit();
}
?>
