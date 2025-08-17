<?php
session_start();
require_once './utils/db.php';

if (!isset($_SESSION['userid']) && isset($_COOKIE['rememberme'])) {
    $token = $_COOKIE['rememberme'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE remember_token = ?");
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user) {
        $_SESSION['userid'] = $user['id'];
        $_SESSION['user'] = $user;
        $_SESSION['name'] = $user['name'];
    }
}

if (isset($_SESSION['userid'])) {
    header("Location: ./home.php");
} else {
    header("Location: ./utils/login.php");
}
exit;
?>