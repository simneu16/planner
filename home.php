<?php
session_start();
if (!isset($_SESSION["userid"])) {
    header("location: ./utils/login.php");
    exit;
}

require "./includes/navbar.php";
require_once "./utils/db.php";

$result = mysqli_query(
    $conn,
    "
    SELECT e.id, e.nazov, 
           DATE_FORMAT(e.od, '%d.%m.%Y, %H:%i') AS od,
           DATE_FORMAT(e.do, '%d.%m.%Y, %H:%i') AS do_time
    FROM events e
    GROUP BY e.id
    ORDER BY e.od ASC
"
);

$events = mysqli_fetch_all($result, MYSQLI_ASSOC);

$userid = (int) $_SESSION["userid"];
$myResult = mysqli_query(
    $conn,
    "
    SELECT e.id, e.nazov, 
           DATE_FORMAT(e.od, '%d.%m.%Y, %H:%i') AS od,
           DATE_FORMAT(e.do, '%d.%m.%Y, %H:%i') AS do_time
    FROM events e
    INNER JOIN event_signups es ON e.id = es.event_id
    WHERE es.user_id = $userid
    ORDER BY e.od ASC
"
);
$myEvents = mysqli_fetch_all($myResult, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>ssostaTV Planner | Domov</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="./styles.css">
    <script src="script.js"></script>
</head>

<body>
    <div class="container">
        <button id="enableNotifications">Povoliť upozornenia</button>
        <script>
            document.getElementById("enableNotifications").addEventListener("click", () => {
                if ('serviceWorker' in navigator && 'PushManager' in window) {
                    navigator.serviceWorker.register('./utils/service_worker.js').then(swReg => {
                        console.log('Service Worker registrovaný:', swReg);

                        Notification.requestPermission().then(permission => {
                            if (permission === 'granted') {
                                swReg.pushManager.subscribe({
                                    userVisibleOnly: true,
                                    applicationServerKey: "BP4d9reUCeBwk6dLR727vt16ne56auW0FOBgx-5N-CCxpFS5hxYIftuoR5d96CEtqeeCtSNqxnkyviU3R9dIKAU"
                                }).then(subscription => {
                                    fetch('./utils/save_subscription.php', {
                                        method: 'POST',
                                        headers: { 'Content-Type': 'application/json' },
                                        body: JSON.stringify(subscription)
                                    });
                                });
                            }
                        });
                    });
                }
            });
        </script>
        <div class="row">
            <div class="col-12">
                <div class="p-2">
                    <h3>
                        <?php echo "Vitaj, " . htmlspecialchars($_SESSION["name"]) . "!"; ?>
                    </h3>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="p-2">
                    <h4>
                        Nadchádzajúce akcie
                    </h4>
                </div>
            </div>
        </div>
        <div class="row">
            <?php foreach ($events as $event): ?>
                <div class="col-12">
                    <div class="card mb-3 bg-light-subtle">
                        <div class="col-12">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($event['nazov']) ?></h5>
                                <div class="card-text">
                                    <small><b><?= htmlspecialchars($event['od']) ?> -
                                            <?= htmlspecialchars($event['do_time']) ?></b>
                                    </small>
                                </div>
                                <br>
                                <div class="d-flex flex-row-reverse">
                                    <a href="./events.php">
                                        <button type="button" class="btn btn-success btn-sm"><i
                                                class="fa fa-arrow-right"></i></button>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <br>
        <div class="row">
            <div class="col-12">
                <div class="p-2">
                    <h4>
                        Moje akcie
                    </h4>
                </div>
            </div>
            <?php if (!empty($myEvents)): ?>
                <?php foreach ($myEvents as $event): ?>
                    <div class="col-12">
                        <div class="card mb-3 bg-light">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($event['nazov']) ?></h5>
                                <div class="card-text">
                                    <small><b><?= htmlspecialchars($event['od']) ?> -
                                            <?= htmlspecialchars($event['do_time']) ?></b></small>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <p>Zatiaľ nie si prihlásený/-á na žiadne akcie.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>