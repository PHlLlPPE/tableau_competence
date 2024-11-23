<?php
require 'auth.php';
require 'db.php';
require_login();

$user_id = $_SESSION['user_id']; // ID de l'utilisateur connecté
$message = ''; // Variable pour stocker le message de confirmation ou d'erreur

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['skill_id'], $_POST['level'])) {
    $skill_id = (int)$_POST['skill_id'];
    $level = (int)$_POST['level'];

    // Vérifier si l'utilisateur est administrateur ou possède la compétence
    $stmt = $pdo->prepare('SELECT user_id FROM skills WHERE id = ?');
    $stmt->execute([$skill_id]);
    $skill = $stmt->fetch();

    if ($skill && (is_admin() || $skill['user_id'] == $user_id)) {
        $stmt = $pdo->prepare('UPDATE skills SET level = ? WHERE id = ?');
        $stmt->execute([$level, $skill_id]);
        $message = "Le niveau de compétence a été mis à jour avec succès.";
    } else {
        $message = "Erreur : Vous n’avez pas les autorisations nécessaires.";
    }
}

// Récupérer toutes les compétences uniques (colonnes dynamiques)
$skills = $pdo->query('SELECT DISTINCT skill_name FROM skills ORDER BY skill_name')->fetchAll(PDO::FETCH_COLUMN);

// Récupérer les utilisateurs et leurs compétences
$users = $pdo->query('
    SELECT u.id AS user_id, u.username, s.skill_name, s.level, s.id AS skill_id
    FROM users u
    LEFT JOIN skills s ON u.id = s.user_id
    WHERE u.is_admin = 0
    ORDER BY u.username, s.skill_name
')->fetchAll();

// Organiser les données par utilisateur
$grouped_skills = [];
foreach ($users as $row) {
    $grouped_skills[$row['username']][$row['skill_name']] = $row;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau des compétences</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        
    </style>
</head>
<body>
    <main>
        <h1 class="titretable">Tableau des compétences</h1>
        <table>
            <thead>
                <tr>
                    <th>Utilisateur</th>
                    <?php foreach ($skills as $skill): ?>
                        <th><?= htmlspecialchars($skill) ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($grouped_skills as $username => $user_skills): ?>
                    <tr>
                        <td><?= htmlspecialchars($username) ?></td>
                        <?php foreach ($skills as $skill_name): ?>
                            <td>
                                <?php if (isset($user_skills[$skill_name])): ?>
                                    <?php $skill = $user_skills[$skill_name]; ?>
                                    <?php if ($skill['user_id'] == $user_id || is_admin()): ?>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="skill_id" value="<?= $skill['skill_id'] ?>">
                                            <select name="level">
                                                <option value="1" <?= $skill['level'] == 1 ? 'selected' : '' ?>>1</option>
                                                <option value="2" <?= $skill['level'] == 2 ? 'selected' : '' ?>>2</option>
                                                <option value="3" <?= $skill['level'] == 3 ? 'selected' : '' ?>>3</option>
                                            </select>
                                            <button type="submit">Modifier</button>
                                        </form>
                                    <?php else: ?>
                                        <?= htmlspecialchars($skill['level']) ?>
                                    <?php endif; ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <br>
        <button type="submit" class="deconnexion"><a href="logout.php" class="deco">Déconnexion</a></button>
    </main>

    <!-- Popup de confirmation -->
    <div id="overlay"></div>
    <div id="popup">
        <p id="popup-message"><?= htmlspecialchars($message) ?></p>
        <button onclick="closePopup()">OK</button>
    </div>

    <footer>
    2024 &copy; Philippe Gaulin | Tout droit réservé.
        <a href="https://github.com/PHlLlPPE" target="_blank" style="color: #ffffff;">
            <i class='bx bxl-github'></i>
        </a>
    </footer>

    <script>
        // Affiche le popup si un message est défini
        document.addEventListener('DOMContentLoaded', function() {
            const message = <?= json_encode($message) ?>;
            if (message) {
                document.getElementById('popup-message').textContent = message;
                document.getElementById('popup').classList.add('active');
                document.getElementById('overlay').classList.add('active');
            }
        });

        // Fonction pour fermer le popup
        function closePopup() {
            document.getElementById('popup').classList.remove('active');
            document.getElementById('overlay').classList.remove('active');
        }
    </script>
</body>
</html>