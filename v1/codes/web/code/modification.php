<?php
session_start();
include_once "fonctions.php";
include_once "formulaires.php";
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DVDThèque - Modification</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="js/modification.js"></script>
</head>

<body>

    <header>
        <h1>DVDThèque - Modification</h1>
        <?php
        if (!empty($_SESSION) && isset($_SESSION["mail"]) && isset($_SESSION["statut"])) {
            echo '<p>Connecté en tant que ' . $_SESSION["mail"] . '</p>';
            ?>
            <form method="POST" action="connexion.php">
                <button type="submit" name="deconnexion" value="true">Déconnexion</button>
            </form>

            <?php

            if ($_SESSION["statut"] == "admin") {
                echo "<p>Statut: Administrateur <p>";
            } else {
                echo "<p>Statut: Utilisateur</p>";
            }
        } else {
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
            } else {
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
        } else if (isset($_SESSION) && isset($_SESSION["statut"]) && $_SESSION["statut"] != "admin") {
            echo "<p>Non autorisé. Redirection...";
            redirect("index.php", 2);
        } else {
            echo "<h2>Modifier des informations</h2>";
            formulaire_choix_film();
            formulaire_choix_realisateur();

            $db = new PDO("sqlite:db/DVDTheque.sqlite");
            // un film a été modifié
            if (isset($_POST) && isset($_POST["idFilm"]) && isset($_POST["nomFilm"]) && isset($_POST["dateSortie"]) && isset($_POST["genreFilm"]) && isset($_POST["realisateur"]) && isset($_POST["captcha"]) && $_POST["captcha"] == $_SESSION["code"]) {
                // mise à jour des informations du film dans la BDD
        
                // on traite les entréees utilistaeur pour éviter les injections SQL
                $idFilm = intval($_POST["idFilm"]);
                $nomFilm = $db->quote($_POST["nomFilm"]);
                $dateSortie = $db->quote($_POST["dateSortie"]);
                $genreFilm = $db->quote($_POST["genreFilm"]);
                $realisateur = $db->quote($_POST["realisateur"]);
                $lienAffiche = $db->quote("images/$idFilm.jpg");

                $sql = "UPDATE Film SET Titre=$nomFilm, DateSortie=$dateSortie, Genre=$genreFilm, NoRealisateur=$realisateur, LienAffiche=$lienAffiche WHERE NoFilm=$idFilm;";
                try {
                    $res = $db->exec($sql);
                } catch (PDOException $e) {
                    echo "Erreur Base de données: $e";
                }
                
                // gérer l'éventuel file upload (affiche)
                if (isset($_FILES["affiche"]) && $_FILES["affiche"]["error"] == UPLOAD_ERR_OK) {
                    $nomTemporaireAffiche = $_FILES["affiche"]["tmp_name"];
                    $cheminDestination = "images/{$idFilm}.jpg";

                    // on vérifie le type MIME pour éviter des injections de fichiers malveillants
                    $mimeType = mime_content_type($nomTemporaireAffiche);
                    if ($mimeType == "image/jpeg") {
                        move_uploaded_file($nomTemporaireAffiche, $cheminDestination);
                    } else {
                        echo "Erreur : vérifier votre image";
                    }


                }
                else if (isset($_POST) && isset($_POST["idFilm"]) && isset($_POST["nomFilm"]) && isset($_POST["dateSortie"]) && isset($_POST["genreFilm"]) && isset($_POST["realisateur"]) && isset($_POST["captcha"]) && $_POST["captcha"] != $_SESSION["code"]) {
                    echo "<p>Captcha incorrect. Données non modifiées";
                }
                    $tableau = getFilmEtRealisateur();
                    if (isset($res) && $res) {
                        echo "<p>Film modifié avec succès. Nouvelles données: </p>";
                        afficheTableauAvecAffiche($tableau);
                    } else if (!isset($res)) {
                        echo "<p>Erreur lors de la modification du film. Données: </p>";
                        afficheTableauAvecAffiche($tableau);
                    }
            }

            // un réalisateur a été modifié
            if (isset($_POST) && isset($_POST["nomReal"]) && isset($_POST["prenomReal"]) && isset($_POST["paysReal"]) && isset($_POST["captcha"]) && $_POST["captcha"] == $_SESSION["code"]) {
                $idReal = $_POST["idReal"];
                $nomReal = $db->quote($_POST["nomReal"]);
                $prenomReal = $db->quote($_POST["prenomReal"]);
                $paysReal = $db->quote($_POST["paysReal"]);

                $sql = "UPDATE Realisateur SET Nom=$nomReal, Prenom=$prenomReal, Pays=$paysReal WHERE NoRealisateur = $idReal ;";
                $res = $db->query($sql);
                if ($res) {
                    echo "<p>Réalisateur modifié avec succès. Nouvelles informations : </p>";
                    $res_tab_reals = $db->query("SELECT Nom, Prenom, Pays FROM Realisateur");
                    if ($res_tab_reals) {
                        $tab_reals = $res_tab_reals->fetchAll(PDO::FETCH_ASSOC);
                    }
                    afficheTableau($tab_reals);
                }
            }
            else if (isset($_POST) && isset($_POST["nomReal"]) && isset($_POST["prenomReal"]) && isset($_POST["paysReal"]) && isset($_POST["captcha"]) && $_POST["captcha"] != $_SESSION["code"]) {
                echo "<p>Captcha incorrect. Informations non modifiées.";
            }

        }
        ?>

        <div id="formulaire">
            <!-- Cette div affichera avec AJAX le formulaire de modification du réalisateur ou du film choisi plus haut -->
        </div>

    </article>

    <hr>
    <footer>
        <p>Application développée par Valentin PIERRE et Thomas LE FLOC'H</p>
        <p>R&T Lannion - 2025</p>
    </footer>
</body>

</html>