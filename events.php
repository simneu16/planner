<?php
session_start();
require "./includes/navbar.php";
require_once "./utils/db.php";

$result = mysqli_query(
    $conn,
    "
    SELECT e.id, e.nazov, 
           DATE_FORMAT(e.od, '%d.%m.%Y, %H:%i') AS od,
           DATE_FORMAT(e.do, '%d.%m.%Y, %H:%i') AS do_time,
           e.ucebna, e.kamera, e.redaktor, e.foto, e.zvuk, e.reels,
           GROUP_CONCAT(u.name SEPARATOR ', ') AS signed_up_names,
           GROUP_CONCAT(u.id) AS signed_up_ids
    FROM events e
    LEFT JOIN event_signups es ON e.id = es.event_id
    LEFT JOIN users u ON es.user_id = u.id
    WHERE e.od >= NOW()
    GROUP BY e.id
    ORDER BY e.od ASC
"
);

$events = mysqli_fetch_all($result, MYSQLI_ASSOC);

for ($i = 0; $i < count($events); $i++) {
    $events[$i]['signed_up_ids'] = $events[$i]['signed_up_ids'] ? explode(',', $events[$i]['signed_up_ids']) : [];
    $events[$i]['signed_up_ids'] = array_map('intval', $events[$i]['signed_up_ids']);
    $events[$i]['is_user_signed_up'] = isset($_SESSION['userid']) && in_array((int) $_SESSION['userid'], $events[$i]['signed_up_ids']);
}
?>
<!DOCTYPE html>
<html lang="sk">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>ssostaTV Planner | Nadchádzajúce akcie</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css">
    <link rel="manifest" href="./manifest.json">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="./script.js"></script>
</head>

<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Nadchádzajúce akcie</h3>
        </div>
        <?php foreach ($events as $event): ?>
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($event['nazov']) ?></h5>
                    <p class="card-text"><b><?= htmlspecialchars($event['od']) ?> -
                            <?= htmlspecialchars($event['do_time']) ?></b></p>
                    <p class="card-text">Učebňa: <?= htmlspecialchars($event['ucebna']) ?></p>
                    <p class="card-text"><?= $event['kamera'] ? 'Kamera, ' : '' ?>
                    <?= $event['redaktor'] ? 'Redaktor, ' : '' ?>
                    <?= $event['foto'] ? 'Foto, ' : '' ?>
                    <?= $event['zvuk'] ? 'Zvuk, ' : '' ?>
                    <?= $event['reels'] ? 'Reels' : '' ?></p>
                    <p class="card-text">Prihlásený(-í):
                        <?= $event['signed_up_names'] ? htmlspecialchars($event['signed_up_names']) : 'Nikto' ?></p>

                    <?php if ($_SESSION['role'] !== 'admin'): ?>
                        <?php if ($event['is_user_signed_up']): ?>
                            <a href="./utils/recall.php?id=<?= $event['id'] ?>" class="btn btn-warning btn-sm"
                                onclick="return confirm('Naozaj sa chceš odhlásiť z akcie?');">Odhlásiť sa</a>
                        <?php else: ?>
                            <a href="./utils/signup.php?id=<?= $event['id'] ?>" class="btn btn-primary btn-sm">Prihlásiť sa</a>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <a href="./utils/edit_event.php?id=<?= $event['id'] ?>" class="btn btn-info btn-sm">Upraviť</a>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <a href="./utils/delete_event.php?id=<?= $event['id'] ?>" class="btn btn-danger btn-sm"
                            onclick="return confirm('Naozaj chcete odstrániť túto akciu?');">Odstrániť akciu</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <a href="./utils/create_event.php" class="btn btn-success">Pridať akciu</a>
        <?php endif; ?>
    </div>
</body>

</html>