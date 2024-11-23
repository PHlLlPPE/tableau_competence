<?php
require 'auth.php';
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare('SELECT id, password_hash, is_admin FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['is_admin'] = (bool)$user['is_admin']; // Stocker le rôle administrateur
        header('Location: index.php');
        exit;
    } else {
        $error = 'Nom d’utilisateur ou mot de passe incorrect.';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <meta charset="UTF-8">
    <title>Connexion</title>
</head>
<body>
    <h1>Connexion</h1>
    <?php if (!empty($error)) echo "<p style='color: red;'>$error</p>"; ?>
    <form method="POST" class="formlogin">
        <input type="text" name="username" placeholder="Nom d'utilisateur" required>
        <input type="password" name="password" placeholder="Mot de passe" required>
        <button type="submit" class="connexion">Se connecter</button>
        <p class="plogin">Pas encore de compte ? <a href="register.php" class="connect"> Cliquez ici pour en créer un !</a></p>
    </form>
    <h1 class="troll">Vous verrez, c'est mieux que les post-it !</h1>
    <img src="post-it.png" alt="post-it">
    <footer>
    &copy; 2024 Philippe Gaulin <a href="https://github.com/PHlLlPPE" target="_blank" style="color: #ffffff;">
    <i class='bx bxl-github'></i>
</a>
    </footer>
</body>
</html>
