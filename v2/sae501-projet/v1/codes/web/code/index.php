<?php
    session_start();
    include_once "fonctions.php";

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DVDThèque - Accueil</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    
    <header>
        <h1>DVDThèque - Accueil</h1>
        <?php
        if(!empty($_SESSION) && isset($_SESSION["mail"]) && isset($_SESSION["statut"])) {
                echo '<p>Connecté en tant que '.$_SESSION["mail"].'</p>';
                ?>
                <form method="POST" action="connexion.php">
                    <button type="submit" name="deconnexion" value="true">Déconnexion</button>
                </form>

                <?php

                if ($_SESSION["statut"] == "admin") {
                    echo "<p>Statut: Administrateur <p>";
                }
                else {
                    echo "<p>Statut: Utilisateur</p>";
                }
            }
            else {
                echo "<p>Statut: Déconnecté</p>";
            }
        ?>
        <hr>
    </header>

    <nav>
        <?php
        if (isset($_SESSION) && isset($_SESSION["statut"])) {
            // si le statut est admin, on affiche toutes les pages
            if ($_SESSION["statut"] == "admin") {
                echo '<a href="index.php">Menu principal</a><br>';
                echo '<a href="connexion.php">Page de connexion</a><br>';
                echo '<a href="insertion.php">Insérer des DVDs</a><br>';
                echo '<a href="modification.php">Modifier des DVDs</a><br>';
                echo '<a href="suppression.php">Supprimer des DVDs</a><br>';
            }
            else {
                // sinon, seulement certaines
                echo '<a href="index.php">Menu principal</a><br>';
                echo '<a href="connexion.php">Page de connexion</a><br>';
            }
        }
        ?>
        <hr>
    </nav>

    <article>
        <?php 
        if (empty($_SESSION)) {
            echo "<p>Déconnecté. Redirection...";
            redirect("connexion.php", 2);
        } else {
            echo "<h2>DVDs en Stock</h2>";
            $dvdEtReals = getFilmEtRealisateur();
            afficheTableauAvecAffiche($dvdEtReals);
        }

        ?>
        <hr>
    </article>


    <footer>
        <p>Application développée par Valentin PIERRE et Thomas LE FLOC'H</p>
        <p>R&T Lannion - 2025</p>
    </footer>
</body>
</html>