<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Vérifier si les mots de passe correspondent
    if ($password !== $confirm_password) {
        $error = "Les mots de passe ne correspondent pas.";
    } else {
        // Vérifier si le nom d'utilisateur est déjà pris
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username = ?');
        $stmt->execute([$username]);
        if ($stmt->fetchColumn() > 0) {
            $error = "Ce nom d'utilisateur est déjà pris.";
        } else {
            // Hacher le mot de passe
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // Insérer le nouvel utilisateur
            $stmt = $pdo->prepare('INSERT INTO users (username, password_hash) VALUES (?, ?)');
            $stmt->execute([$username, $password_hash]);

            // Récupérer l'ID du nouvel utilisateur
            $user_id = $pdo->lastInsertId();

            // Ajouter des compétences par défaut
            $stmt = $pdo->prepare('
                INSERT INTO skills (user_id, skill_name, level)
                VALUES 
                (?, "Maquetter des interfaces", 1),
                (?, "Interface Utilisateur", 1),
                (?, "Développer la partie dynamique", 1),
                (?, "Installer et configurer son environnement", 1),
                (?, "Développer les composant SQL / NoSQL", 1),
                (?, "Développer des composants coté serveur", 1),
                (?, "Documenter le déploiement", 1),
                (?, "Mettre en place une DB", 1)
            ');
            $stmt->execute([$user_id, $user_id, $user_id, $user_id, $user_id, $user_id, $user_id, $user_id]);

            // Rediriger vers la page de connexion
            header('Location: login.php');
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <meta charset="UTF-8">
    <title>Inscription</title>
</head>
<body>
    <main>
    <h1>Créer un compte</h1>
    <?php if (!empty($error)) echo "<p style='color: red;'>$error</p>"; ?>
    <form method="POST" class="formregister">
        <label>Nom d'utilisateur :</label>
        <input type="text" name="username" required>
        <br>
        <label>Mot de passe :</label>
        <input type="password" name="password" required>
        <br>
        <label>Confirmer le mot de passe :</label>
        <input type="password" name="confirm_password" required>
        <br>
        <button type="submit" class="register">S'inscrire</button>
        <p class="pregister">Déjà un compte ? <a href="login.php" class="connect">Connectez-vous ici</a>.</p>
    </form>
    </main>
    <footer>
    &copy; 2024 Philippe Gaulin <a href="https://github.com/PHlLlPPE" target="_blank" style="color: #ffffff;">
    <i class='bx bxl-github'></i>
</a>
    </footer>
</body>
</html>
