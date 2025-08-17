<?php
session_start();
require_once "./db.php";

if (!isset($_SESSION['userid'])) die("Musíte byť prihlásený.");
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) die("Neplatné ID akcie.");

$event_id = (int)$_GET['id'];
$user_id = (int)$_SESSION['userid'];

$stmt_check = mysqli_prepare($conn, "SELECT 1 FROM event_signups WHERE event_id = ? AND user_id = ?");
mysqli_stmt_bind_param($stmt_check, "ii", $event_id, $user_id);
mysqli_stmt_execute($stmt_check);
mysqli_stmt_store_result($stmt_check);
if (mysqli_stmt_num_rows($stmt_check) > 0) {
    mysqli_stmt_close($stmt_check);
    header("Location: ../events.php");
    exit;
}
mysqli_stmt_close($stmt_check);

$stmt = mysqli_prepare($conn, "INSERT INTO event_signups (event_id, user_id) VALUES (?, ?)");
mysqli_stmt_bind_param($stmt, "ii", $event_id, $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

header("Location: ../events.php");
exit;
?>