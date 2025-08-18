<?php
session_start();
require_once "./db.php";

if ($_SESSION['role'] !== 'admin') {
    die("Prístup zamietnutý.");
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Neplatné ID.");
}

$id = (int) $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nazov = $_POST['nazov'];
    $od = $_POST['od'];
    $do = $_POST['do'];
    $ucebna = $_POST['ucebna'];
    $kamera = isset($_POST['kamera']) ? 1 : 0;
    $redaktor = isset($_POST['redaktor']) ? 1 : 0;
    $foto = isset($_POST['foto']) ? 1 : 0;
    $zvuk = isset($_POST['zvuk']) ? 1 : 0;
    $reels = isset($_POST['reels']) ? 1 : 0;

    $stmt = mysqli_prepare($conn, "UPDATE events SET nazov=?, od=?, do=?, ucebna=?, kamera=?, redaktor=?, foto=?, zvuk=?, reels=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "ssssiiiiii", $nazov, $od, $do, $ucebna, $kamera, $redaktor, $foto, $zvuk, $reels, $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: ../events.php");
    exit;
}

$result = mysqli_query($conn, "SELECT * FROM events WHERE id = $id");
$event = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="manifest" href="./manifest.json">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <h2>Upraviť akcie</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="nazov" class="form-label">Názov:</label>
                <input type="text" id="nazov" name="nazov" class="form-control"
                    value="<?= htmlspecialchars($event['nazov']) ?>" required>
            </div>

            <div class="mb-3">
                <label>Od:</label>
                <input type="datetime-local" name="od" class="form-control"
                    value="<?= date('Y-m-d\TH:i', strtotime($event['od'])) ?>" required>
            </div>

            <div class="mb-3">
                <label>Do:</label>
                <input type="datetime-local" name="do" class="form-control"
                    value="<?= date('Y-m-d\TH:i', strtotime($event['do'])) ?>" required>
            </div>

            <div class="mb-3">
                <label>Učebňa:</label>
                <input type="text" name="ucebna" class="form-control" value="<?= htmlspecialchars($event['ucebna']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="funkcie" class="form-label">Funckie</label>
                <div class="btn-group-sm" role="group">
                    <input type="checkbox" name="kamera" id="kamera" class="btn-check" <?= $event['kamera'] ? 'checked' : '' ?>>
                    <label for="kamera" class="btn btn-outline-info">Kamera</label>

                    <input type="checkbox" name="redaktor" id="redaktor" class="btn-check" <?= $event['redaktor'] ? 'checked' : '' ?>>
                    <label for="redaktor" class="btn btn-outline-info">Redaktor</label>

                    <input type="checkbox" name="foto" id="foto" class="btn-check" <?= $event['foto'] ? 'checked' : '' ?>>
                    <label for="foto" class="btn btn-outline-info">Foto</label>

                    <input type="checkbox" name="zvuk" id="zvuk" class="btn-check" <?= $event['zvuk'] ? 'checked' : '' ?>>
                    <label for="zvuk" class="btn btn-outline-info">Zvuk</label>

                    <input type="checkbox" name="reels" id="reels" class="btn-check" <?= $event['reels'] ? 'checked' : '' ?>>
                    <label for="reels" class="btn btn-outline-info">Reels</label>
                </div>
            </div>

            <button type="submit">Uložiť</button>
        </form>
    </div>

</body>

</html>