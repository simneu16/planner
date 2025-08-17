<?php
session_start();
require_once "./db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Prístup zamietnutý.");
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Neplatné ID akcie.");
}

$id = (int) $_GET['id'];

$stmt = mysqli_prepare($conn, "DELETE FROM event_signups WHERE event_id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$stmt = mysqli_prepare($conn, "DELETE FROM events WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

header("Location: ../events.php");
exit;
?>