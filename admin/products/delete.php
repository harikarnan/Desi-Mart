<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION['admin'])) {
    header('Location: ../admin_login.php');
    exit();
}

$db = (new Database())->getConnection();
$product_id = $_GET['id'];

$query = "DELETE FROM products WHERE product_id = :product_id";
$stmt = $db->prepare($query);
$stmt->execute([':product_id' => $product_id]);

header('Location: index.php');
exit();
?>
