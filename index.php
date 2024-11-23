<?php
require 'auth.php';
require 'db.php';
require_login();

$user_id = $_SESSION['user_id']; // ID de l'utilisateur connecté

// Traitement des requêtes POST pour modifier les niveaux de compétence
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
        echo "<p style='color: green;'>Le niveau de compétence a été mis à jour avec succès.</p>";
    } else {
        echo "<p style='color: red;'>Erreur : Vous n’avez pas les autorisations nécessaires.</p>";
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
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <meta charset="UTF-8">
    <title>Tableau des compétences</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <main>
        <h1>Tableau des compétences</h1>
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
        <footer>
        &copy; 2024 Philippe Gaulin <a href="https://github.com/PHlLlPPE" target="_blank" style="color: #ffffff;">
    <i class='bx bxl-github'></i>
</a>
        </footer>
</body>
</html>
