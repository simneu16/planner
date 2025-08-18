<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ./login.php");
    exit;
}

require_once "./db.php";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: ../admin_panel.php?msg=Neplatné ID používateľa.");
    exit;
}

$user_id = (int)$_GET['id'];

$stmt = $conn->prepare("DELETE FROM event_signups WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->close();

$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
if ($stmt->execute()) {
    $stmt->close();
    header("Location: ../admin_panel.php?msg=Používateľ bol odstránený");
    exit;
} else {
    $stmt->close();
    header("Location: ../admin_panel.php?msg=Chyba pri odstraňovaní používateľa");
    exit;
}
?>