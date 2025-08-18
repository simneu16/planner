<?php

require_once "./db.php";
require_once "./session.php";


$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {

    $nick = trim($_POST['nick']);
    $password = trim($_POST['password']);

    if (empty($nick)) {
        $error .= '<p class="error">Zadaj nick.</p>';
    }

    if (empty($password)) {
        $error .= '<p class="error">Zadaj heslo.</p>';
    }

    if (empty($error)) {
        if ($query = $conn->prepare("SELECT * FROM users WHERE nick = ?")) {
            $query->bind_param('s', $nick);
            $query->execute();
            $result = $query->get_result();
            $row = $result->fetch_assoc();
            if ($row && isset($row['password'])) {
                if (password_verify($password, $row['password'])) {
                    $_SESSION["userid"] = $row['id'];
                    $_SESSION["user"] = $row;
                    $_SESSION["name"] = $row['name'];
                    $_SESSION['role'] = $row['role'];

                    if (isset($_POST['remember'])) {
                        $token = bin2hex(random_bytes(16));
                        $expiry = time() + (86400 * 30);

                        if ($stmt = $conn->prepare("UPDATE users SET remember_token = ? WHERE id = ?")) {
                            $stmt->bind_param('si', $token, $row['id']);
                            $stmt->execute();
                            $stmt->close();
                        }

                        setcookie("rememberme", $token, $expiry, "/", "", false, true);
                    }

                    header("location: ../home.php");
                    exit;
                } else {
                    $error .= '<p class="error">Nesprávne heslo.</p>';
                }
            } else {
                $error .= '<p class="error">Užívateľ nenájdený.</p>';
            }
        }
        $query->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>ssostaTV Planner | Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="../styles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="../script.js"></script>
    <link rel="manifest" href="./manifest.json">
</head>

<body>
    <div id="content" class="container">
        <div>
            <div class="form-container">
                <div class="row justify-content-center">
                    <div class="col-md-6 col-sm-12">
                        <form action="" method="post" class="login">
                            <h2>Prihlásenie</h2>
                            <p class="text-danger"><?php echo $error; ?></p>
                            <div class="form-group">
                                <label for="nick">Nick</label>
                                <input type="text" name="nick" id="nick" class="form-control" required />
                            </div>
                            <div class="form-group">
                                <label for="password">Heslo</label>
                                <input type="password" name="password" id="password" class="form-control" required>
                            </div>
                            <div class="form-group form-check">
                                <input type="checkbox" name="remember" id="remember" class="form-check-input">
                                <label class="form-check-label" for="remember">Zapamätať si ma</label>
                            </div>
                            <div class="form-group">
                                <input type="submit" name="submit" value="Prihlásiť sa" class="btn btn-primary btn-block">
                            </div>
                            <p>Ešte nemáš účet? <a href="./register.php">Zaregistruj sa.</a></p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>