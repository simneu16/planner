<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="manifest" href="./manifest.json">

</head>

<body>
  <?php
  $activePage = basename($_SERVER['PHP_SELF']);
  $activePage = str_replace('.php', '', $activePage);
  ?>
  <nav class="navbar-bottom-app">
    <a class="nav-link py-1 <?php if ($activePage === 'home')
      echo 'active'; ?>" href="./home.php"><i
        class="fa fa-home"></i></a>
    <a class="nav-link <?php if ($activePage === 'events')
      echo 'active'; ?>" href="./events.php"><i
        class="fa fa-calendar-check-o"></i></a>
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') { ?>
      <a class="nav-link <?php if ($activePage === 'admin_panel')
        echo 'active'; ?>" href="./admin_panel.php"><i
          class="fa fa-unlock-alt" aria-hidden="true"></i></a>
    <?php } ?>
    <a class="nav-link <?php if ($activePage === 'logout')
      echo 'active'; ?>" href="./utils/logout.php"><i
        class="fa fa-sign-out"></i></a>
  </nav>
</body>

</html>