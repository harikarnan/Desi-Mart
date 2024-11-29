<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION['admin'])) {
    header('Location: ../admin_login.php');
    exit();
}

$category_id = $_GET['id'];
$db = (new Database())->getConnection();

$query = "DELETE FROM categories WHERE category_id = :category_id";
$stmt = $db->prepare($query);
$stmt->execute([':category_id' => $category_id]);

header('Location: index.php');
exit();
?>
