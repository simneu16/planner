<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ./utils/login.php");
    exit;
}

require_once "./utils/db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['role'])) {
    $user_id = (int) $_POST['user_id'];
    $new_role = $_POST['role'];
    if (in_array($new_role, ['admin', 'člen'])) {
        $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->bind_param('si', $new_role, $user_id);
        $stmt->execute();
        $stmt->close();
        $message = "Rola používateľa bola aktualizovaná.";
    } else {
        $message = "Neplatná rola.";
    }
}


$result = $conn->query("SELECT id, nick, name, role FROM users");
?>

<!DOCTYPE html>
<html lang="sk">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>ssostaTV Planner | Admin panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css">
    <link rel="manifest" href="./manifest.json">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="./script.js"></script>
</head>

<body>
    <?php require "./includes/navbar.php"; ?>

    <div class="container">
        <h3>Admin panel - Správa používateľov</h3>

        <?php if (!empty($message)): ?>
            <div>
                <p class="text-info"><?php echo htmlspecialchars($message); ?></p>
            </div>
        <?php endif; ?>

        <table class="table table-bordered mx-auto">
            <thead>
                <tr>
                    <th>Nick</th>
                    <th>Meno</th>
                    <th>Rola</th>
                    <th>Zmena</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['nick']); ?></td>
                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                        <td><?php echo htmlspecialchars($user['role']); ?></td>
                        <td>
                            <form method="post">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <select name="role">
                                    <option value="člen" <?php if ($user['role'] === 'admin')
                                        echo 'selected'; ?>>Člen</option>
                                    <option value="admin" <?php if ($user['role'] === 'člen')
                                        echo 'selected'; ?>>Admin
                                    </option>
                                </select>
                                <button class="btn btn-primary btn-sm" type="submit">Uložiť</button>
                            </form>
                            <a href="./utils/remove_user.php?id=<?php echo $user['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Naozaj chcete odstrániť užívateľa?');">Odstrániť</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>

</html>