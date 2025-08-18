<?php
  $activePage = basename($_SERVER['PHP_SELF']);
  $activePage = str_replace('.php', '', $activePage);
?>
<nav class="navbar-bottom-app">
  <a class="nav-link py-1 <?php if ($activePage === 'home') echo 'active';?>" href="./home.php"><i class="fa fa-home"></i></a>
  <a class="nav-link <?php if ($activePage === 'events') echo 'active';?>" href="./events.php"><i class="fa fa-calendar-check-o"></i></a>
  <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') { ?>
    <a class="nav-link <?php if ($activePage === 'admin_panel') echo 'active';?>" href="./admin_panel.php"><i class="fa fa-unlock-alt" aria-hidden="true"></i></a>
  <?php } ?>
  <a class="nav-link <?php if ($activePage === 'logout') echo 'active';?>" href="./utils/logout.php"><i class="fa fa-sign-out"></i></a>
</nav>

<script>
if ('serviceWorker' in navigator && 'PushManager' in window) {
  navigator.serviceWorker.register('../utils/service_worker.js').then(swReg => {
    console.log('Service Worker registrovaný:', swReg);

    Notification.requestPermission().then(permission => {
      if (permission === 'granted') {
        swReg.pushManager.subscribe({
          userVisibleOnly: true,
          applicationServerKey: "BP4d9reUCeBwk6dLR727vt16ne56auW0FOBgx-5N-CCxpFS5hxYIftuoR5d96CEtqeeCtSNqxnkyviU3R9dIKAU"
        }).then(subscription => {
          fetch('/utils/save_subscription.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(subscription)
          });
        });
      }
    });
  });
}
</script>
