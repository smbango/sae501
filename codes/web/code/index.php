<?php
session_start();
require_once('fonctions.php');

$message = '';
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}

if (isset($_POST['login'])) {
    $user = verifierConnexion($_POST['username'] ?? '', $_POST['password'] ?? '');
    if ($user) {
        $_SESSION['user'] = $user['username'];
        $_SESSION['role'] = $user['role'];
    } else {
        $message = "Identifiants invalides.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Liste des joueurs ETU 2025</title>
    <link href="style.css" rel="stylesheet" type="text/css" />
</head>
<body>
    <?php if (!isset($_SESSION['user'])): ?>
        <h1>Connexion à l'application Rugby</h1>
        <?php if ($message) echo "<p style='color:red;'>$message</p>"; ?>
        <form method="post" action="index.php">
            <fieldset>
                <legend>Authentification</legend>
                <p><label>Identifiant : <input type="text" name="username" required></label></p>
                <p><label>Mot de passe : <input type="password" name="password" required></label></p>
                <p><input type="submit" name="login" value="Se connecter"></p>
                <small>Comptes : <code>admin</code> / <code>admin</code> ou <code>etudiant</code> / <code>etudiant</code></small>
            </fieldset>
        </form>
    <?php else: ?>
        <p>Connecté : <strong><?= htmlspecialchars($_SESSION['user']) ?></strong> (rôle : <?= htmlspecialchars($_SESSION['role']) ?>) | <a href="index.php?logout=1">Déconnexion</a></p>
        <h1>Menu principal</h1>
        <ul>
            <li><a href="EX1_ListerJoueurs.php">Lister</a></li>
            <li><a href="EX2_listejoueursParEquipe.php">Lister par équipe</a></li>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <li><a href="EX3_InsererJoueurs.php">Insérer (réservé admin)</a></li>
            <?php endif; ?>
        </ul>
    <?php endif; ?>
</body>
</html>
