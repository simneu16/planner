<?php
session_start();
require_once "./db.php";
require __DIR__ . '/../vendor/autoload.php';
        use Minishlink\WebPush\WebPush;
        use Minishlink\WebPush\Subscription;

error_reporting(0);

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Prístup zamietnutý");
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nazov = trim($_POST['nazov']);
    $od = $_POST['od'];
    $do = $_POST['do'];
    $ucebna = trim($_POST['ucebna']);
    $kamera = isset($_POST['kamera']) ? 1 : 0;
    $redaktor = isset($_POST['redaktor']) ? 1 : 0;
    $foto = isset($_POST['foto']) ? 1 : 0;
    $zvuk = isset($_POST['zvuk']) ? 1 : 0;
    $reels = isset($_POST['reels']) ? 1 : 0;

    if ($nazov && $od && $do && $ucebna) {
        $stmt = mysqli_prepare($conn, "INSERT INTO events (nazov, od, do, ucebna, kamera, redaktor, foto, zvuk, reels) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssssiiiii", $nazov, $od, $do, $ucebna, $kamera, $redaktor, $foto, $zvuk, $reels);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $res = $conn->query("SELECT subscription FROM push_subscriptions");
        $subscriptions = [];
        while ($row = $res->fetch_assoc()) {
            $subscriptions[] = $row['subscription'];
        }

        $auth = [
            'VAPID' => [
                'subject' => 'mailto:admin@domain.sk',
                'publicKey' => 'BP4d9reUCeBwk6dLR727vt16ne56auW0FOBgx-5N-CCxpFS5hxYIftuoR5d96CEtqeeCtSNqxnkyviU3R9dIKAU',
                'privateKey' => 'wWw0NRQamHBSAWCOb3-ydIRZJ6pjBdFa5v_9cfcretQ',
            ],
        ];

        $webPush = new WebPush($auth);

        foreach ($subscriptions as $subJson) {
            $webPush->queueNotification(
                Subscription::create(json_decode($subJson, true)),
                json_encode([
                    'title' => 'Nová akcia',
                    'body' => $nazov . " (" . $od . ")"
                ])
            );
        }

        foreach ($webPush->flush() as $report) {
            if (!$report->isSuccess()) {
                error_log("Push failed: " . $report->getReason());
            }
        }

        header("Location: ../events.php");
        exit;
    } else {
        $error = "Vyplň všetky polia.";
    }
}

require "../includes/navbar.php";
?>
<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pridať akciu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2>Pridať novú akciu</h2>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post">
        <div class="mb-3">
            <label for="nazov" class="form-label">Názov</label>
            <input type="text" name="nazov" id="nazov" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="od" class="form-label">Od</label>
            <input type="datetime-local" name="od" id="od" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="do" class="form-label">Do</label>
            <input type="datetime-local" name="do" id="do" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="ucebna" class="form-label">Učebňa</label>
            <input type="text" name="ucebna" id="ucebna" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Funkcie</label>
            <div class="btn-group-sm" role="group">
                <input type="checkbox" class="btn-check" id="kamera" name="kamera" autocomplete="off">
                <label class="btn btn-outline-info" for="kamera">Kamera</label>

                <input type="checkbox" class="btn-check" id="redaktor" name="redaktor" autocomplete="off">
                <label class="btn btn-outline-info" for="redaktor">Redaktor</label>

                <input type="checkbox" class="btn-check" id="foto" name="foto" autocomplete="off">
                <label class="btn btn-outline-info" for="foto">Fotograf</label>

                <input type="checkbox" class="btn-check" id="zvuk" name="zvuk" autocomplete="off">
                <label class="btn btn-outline-info" for="zvuk">Zvukár</label>

                <input type="checkbox" class="btn-check" id="reels" name="reels" autocomplete="off">
                <label class="btn btn-outline-info" for="reels">Reels</label>
            </div>
        </div>
        <button type="submit" class="btn btn-success">Pridať</button>
    </form>
</div>
</body>
</html>