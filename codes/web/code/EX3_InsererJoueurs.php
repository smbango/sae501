<?php
session_start();
if (!isset($_SESSION['user']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr" >
    <head>
        <meta charset="utf-8">
        <title>Insertion d'un joueur ETU 2025</title>
        <link href="style.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <p><a href="index.php">Retour au menu</a></p>
        <?php
            require_once('fonctions.php');
            afficheFormulaireAjoutjoueur();
            if ((!empty($_POST)) && isset($_POST["joueur"]) && $_POST["joueur"] != "" && isset($_POST["equipe"])) {
                $retour = insererjoueur($_POST["joueur"], $_POST["equipe"]);
                echo "<p> Voici le code retour de l'opération: $retour</p>";
                afficheTableau(listerjoueurs());
            }    
        ?>
    </body>
</html>
