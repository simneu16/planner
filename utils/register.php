<?php

require_once "./db.php";
require_once "./session.php";

error_reporting(0);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {

    $fullname = trim($_POST['name']);
    $nick = trim($_POST['nick']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST["confirm_password"]);
    $password_hash = password_hash($password, PASSWORD_BCRYPT);
    $role = 'člen';

    if ($query = $conn->prepare("SELECT * FROM users WHERE nick = ?")) {
        $error = '';
        $query->bind_param('s', $nick);
        $query->execute();
        $query->store_result();
        if ($query->num_rows > 0) {
            $error .= '<p class="error">Tento nick už existuje.</p>';
        } else {
            if (empty($confirm_password)) {
                $error .= '<p class="error">Prosím potvrď heslo.</p>';
            } else {
                if (empty($error) && ($password != $confirm_password)) {
                    $error .= '<p class="error">Heslá sa nezhodujú.</p>';
                }
            }
            if (empty($error)) {
                $insertQuery = $conn->prepare("INSERT INTO users (name, nick, password, role) VALUES (?, ?, ?, ?);");
                $insertQuery->bind_param("ssss", $fullname, $nick, $password_hash, $role);
                $result = $insertQuery->execute();
                if ($result) {
                    $error = 'Registrácia úspešná!';
                } else {
                    $error = '<p class="error">Ups, niečo sa pokazilo!</p>';
                }
            }
        }
    }
    $query->close();
    mysqli_close($conn);
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>ssostaTV Planner | Registrácia</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="../script.js"></script>
    <link rel="manifest" href="./manifest.json">
</head>

<body>
    <div id="content" class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-sm-12">
                <form action="" method="post" class="login">
                    <h2>Registrácia</h2>
                    <p class="text-info"><?= $error ?></p>
                    <div class="form-group">
                        <label for="name">Meno a priezvisko</label>
                        <input type="text" id="name" name="name" class="form-control" required />
                    </div>
                    <div class="form-group">
                        <label for="nick">Nick (pre prihlásenie)</label>
                        <input type="text" id="nick" name="nick" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Heslo</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Potvrď heslo</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                    </div>
                    <div class="submit">
                        <input type="submit" name="register" value="Registrovať sa" class="btn btn-primary btn-block">
                    </div>
                    <br>
                    <p>Už máš účet? <a href="./login.php">Prihlás sa.</a></p>
                </form>
            </div>
        </div>
    </div>
</body>

</html>