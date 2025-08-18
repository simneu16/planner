<?php
session_start();
require_once "./db.php";

$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    http_response_code(400);
    exit("Invalid data");
}

$userId = $_SESSION['userid'] ?? null;
if (!$userId) {
    http_response_code(403);
    exit("Not logged in");
}

$subJson = json_encode($data);

$stmt = $conn->prepare("REPLACE INTO push_subscriptions (user_id, subscription) VALUES (?, ?)");
$stmt->bind_param("is", $userId, $subJson);
$stmt->execute();
$stmt->close();