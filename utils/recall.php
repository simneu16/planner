

<?php
session_start();
require_once "./db.php";

if (!isset($_SESSION['userid'])) {
    die("Musíte byť prihlásený.");
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Neplatné ID akcie.");
}

$event_id = (int)$_GET['id'];
$user_id = (int)$_SESSION['userid'];

$stmt = mysqli_prepare($conn, "DELETE FROM event_signups WHERE event_id = ? AND user_id = ?");
mysqli_stmt_bind_param($stmt, "ii", $event_id, $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

header("Location: ../events.php");
exit;
?>