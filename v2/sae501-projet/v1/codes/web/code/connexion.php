<?php
    session_start();
    include "fonctions.php";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DVDThèque - Connexion</title>
	<script src="js/connexion.js"></script>
	<link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header>
        <h1>DVDThèque - Connexion</h1>
        <?php
            // on affiche les informations de connexion (s'il y en a) dans le header
            if(!empty($_SESSION) && isset($_SESSION["mail"]) && isset($_SESSION["statut"])) {
                echo '<p>Connecté en tant que '.$_SESSION["mail"].'</p>';
                ?>
                <form method="POST" action="connexion.php">
                    <button type="submit" name="deconnexion" value="true">Déconnexion</button>
                </form>

                <?php

                if ($_SESSION["statut"] == "admin") {
                    echo "<p>Statut: Administrateur </p>";
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

	<!-- Menu de navigation -->
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
		<!-- Formulaire de connexion, affiché uniquement s'il n'y a pas de session -->
		<?php
			if(empty($_SESSION)) {
			?>
				<form action="connexion.php" method="POST" onsubmit="return checkPassword()">
					<label for="mail">Adresse mail:</label>
						<input type="text" name="mail" id="mail">
					<label for="pass">Mot de Passe:</label>
						<input type="password" name="pass" id="pass">
						<input type="submit" value="Envoyer">
				</form>
				<p id="mdp-err"></p>
			<?php
			}

			// s'il y a déjà des infos de connexion, on propose de revenir à l'index
            if(!empty($_SESSION) && isset($_SESSION["mail"]) && isset($_SESSION["statut"])) {
                echo '<p>Vous êtes déjà connecté, <a href="index.php">revenir au menu principal ?</a></p>';
            }
            else {
			// sinon, on teste si on a envoyé des identifiants avec POST
                if (isset($_POST) && isset($_POST["mail"]) && isset($_POST["pass"])) {
                    // la fonction htmlspecialchars permet de sécuriser les entrées utilisateur
                    // en échappant certains caractères
                    // elle patche par exemple les failles XSS
                    $_POST["mail"] = htmlspecialchars($_POST["mail"]); 
                    $_POST["pass"] = htmlspecialchars($_POST["pass"]); 

                    $retour_auth = authentification($_POST["mail"], $_POST["pass"]);
					// renvoie un false si l'authentification a échoué, un tableau associatif contenant l'email et le statut sinon

                    if ($retour_auth) { // = si l'authentification a réussi
                        $_SESSION["mail"] = $retour_auth["EMAIL"];
                        $_SESSION["statut"] = $retour_auth["STATUT"];

                        // ajout du message dans les logs
                        $log_msg =  date("l jS \of F Y h:i:s A: ").$_SESSION["mail"].' s\'est connecté en tant que '.$_SESSION["statut"].' depuis l\'@IP '.$_SERVER["REMOTE_ADDR"];
                        ajouter_log($log_msg);

                        echo '<p>Authentification en tant que '.$_SESSION["mail"].' réussie';
                        echo '<p>Statut: '.$_SESSION["statut"].'</p>';
                        echo "<p>Redirection vers index.php dans 1 seconde...";
                        redirect("index.php", 1);
                    }
                    else { // authentification échouée
                        echo "<p>Authentification échouée. Réessayez.";
                        $log_msg =  date("l jS \of F Y h:i:s A: ").$_POST["mail"].' tentative échouée depuis l\'@IP '.$_SERVER["REMOTE_ADDR"];
                        ajouter_log($log_msg);

                    }
                }
            }

            if (isset($_POST["deconnexion"]) && $_POST["deconnexion"] == true) {
                session_destroy();
                redirect("connexion.php", 0);
            }

        ?>
		<hr>
    </article>
    
	<!-- Pied de page -->
    <footer>
        <p>Application développée par Valentin PIERRE et Thomas LE FLOC'H</p>
        <p>R&T Lannion - 2025</p>
    </footer>
</body>
</html>