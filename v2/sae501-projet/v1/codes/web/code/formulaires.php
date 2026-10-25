<?php
// ce fichier contient les différents formulaires appelés lors des interactions avec nos pages
// concentrer les formulaires ici rend le code plus propre et lisible
// un commentaire indiquera qui a écrit chaque formulaire

function formulaire_choix_film() {
    // écrite par Thomas LE FLOC'H
    $tableau_film = getFilmEtRealisateur();
    ?>
    <form>
        <select name="idFilm" id="choix-film" onchange="getFormModFilm()">
            <option value="0">Choisir un film à modifier</option>
            <?php
                foreach ($tableau_film as $film) {
                    echo '<option value="'.$film['Numéro'].'">'.$film['Titre'].'</option>';
                }
            ?>
        </select>
    </form>
    <br/>
    <?php
}


function formulaire_choix_realisateur() {
    // écrite par Thomas LE FLOC'H
    $db = new PDO("sqlite:db/DVDTheque.sqlite");
    $sql = "SELECT * FROM Realisateur;";
    $res = $db->query($sql);
    if ($res) {
        $tableau_reals = $res->fetchAll(PDO::FETCH_ASSOC);
    }
    ?>
    <select name="idReal" id="choix-real" onchange="getFormModRealisateur()">
        <option value="0">Choisir un réalisateur à modifier</option>
        <?php
            foreach ($tableau_reals as $real) {
                echo '<option value="'.$real['NoRealisateur'].'">'.$real['Prenom'].' '.$real['Nom'].'</option>';
            }
        ?>
    </select>
    </form>
    <br/>
    <?php
}

function formulaire_modification_film() {
    // récupération des données utiles dans la base
    $db = new PDO("sqlite:../db/DVDTheque.sqlite");
    $idFilm = $db->quote($_POST["idFilm"]);

    // récupération des données sur le film
    $sql_film = "SELECT * FROM Film WHERE NoFilm=$idFilm;";
    $res_film = $db->query($sql_film);
    if ($res_film) {
        $tab_film = $res_film->fetch(PDO::FETCH_ASSOC);
    }
    else {
        $tab_film = array();
    }

    // récupération des informations des réalisateurs
    $sql_reals = "SELECT * FROM Realisateur;";
    $res_reals = $db->query($sql_reals);
    if ($res_reals) {
        $tab_reals = $res_reals->fetchAll(PDO::FETCH_ASSOC);
    }

    echo "<h3>Formulaire de modification des informations d'un film</h3>";
    ?>



<form method="POST" action="modification.php" enctype="multipart/form-data">
    <!-- Identifiant du film -->
    <input type="hidden" name="idFilm" value=<?php echo $idFilm ?>>

    <!-- Nom du film -->
        <label for="nomFilm">Nom du film</label>
            <input type="text" name="nomFilm" id="nomFilm" value="<?php echo $tab_film["Titre"] ?>"><br>

    <!-- Date de sortie -->
        <label for="dateSortie">Date de sortie</label>
            <input type="date" name="dateSortie" id="dateSortie"  value=<?php echo $tab_film["DateSortie"] ?>><br>

    <!-- Genre -->
        <label for="genreFilm">Genre:</label>
			<select name="genreFilm" id="genreFilm">
				<?php
				$genres = [
					"Action", "Aventure", "Comédie", "Drame", "Fantastique",
					"Horreur", "Policier", "Science fiction", "Thriller"
				];

				foreach ($genres as $genre) {
					if ($tab_film["Genre"] == $genre) {
						echo '<option value="' . $genre . '" selected>' . $genre . '</option>';
					} else {
						echo '<option value="' . $genre . '">' . $genre . '</option>';
					}
				}
				?>
			</select><br>

        <!-- Réalisateur -->
        <label for="realisateur">Réalisateur: </label>
			<select name="realisateur" id="realisateur">
				<?php
				foreach ($tab_reals as $real) {
					if ($tab_film["NoRealisateur"] == $real["NoRealisateur"]) {
						echo '<option value="'.$real['NoRealisateur'].'" selected>'.$real['Prenom'].' '.$real['Nom'].'</option>';
					} else {
						echo '<option value="'.$real['NoRealisateur'].'">'.$real['Prenom'].' '.$real['Nom'].'</option>';
					}
				}	// TODO: Lien cliquable vers insertion.php pour insérer un réalisateur
				?>
        	</select><br>
            <p>Il faudra <a href="insertion.php">ajouter un réalisateur</a> avant de pouvoir renseigner un de ses films!</p>

        <!-- Affiche -->
        <label for="affiche">Affiche (JPG) :</label>
		<input type="file" name="affiche" id="affiche" accept=".jpg,image/jpeg">
		<p>Affiche actuelle: </p><br>
		<img src="<?php echo $tab_film["LienAffiche"] ?>" alt="Affiche actuelle" style="max-width: 250px"><br>

        <p>Si vous ne souhaitez pas modifier l'affiche, laissez le champ vide.</p> <br>

        <!-- CAPTCHA -->
        <label for="captcha">Êtes-vous un robot ?</label>
        <input type="text" name="captcha" id="captcha"/>
		<img src="captcha.php" onclick="this.src='captcha.php?' + Math.random();" alt="captcha" style="cursor:pointer;">

        <input type="submit" value="Confirmer les modifications">
    </form>

    <?php  
}

function formulaire_modification_realisateur() {
    echo "<h3>Formulaire de modification des infos d'un réalisateur</h3>";
    // récupération des données du réalisateur
    $noRealisateur = $_POST["idReal"];
    $db = new PDO("sqlite:../db/DVDTheque.sqlite");
    $sql = "SELECT * from Realisateur WHERE NoRealisateur=$noRealisateur";
    $res = $db->query($sql);
    if ($res) {
        $tab_real = $res->fetch(PDO::FETCH_ASSOC);
    }
    ?>
    <form action="modification.php" method="post">
        <input type="hidden" name="idReal" value="<?php echo $noRealisateur ?>">    

        <label for="nomReal">Nom du réalisateur:</label>
        <input type="text" name="nomReal" id="nomReal" value="<?php echo $tab_real["Nom"] ?>">
        <br>
        <label for="prenomReal">Prénom du réalisateur: </label>
        <input type="text" name="prenomReal" id="prenomReal" value="<?php echo $tab_real["Prenom"] ?>">
        <br>
        <label for="paysReal">Pays du réalisateur: </label>
        <input type="text" name="paysReal" id="paysReal" value="<?php echo $tab_real["Pays"] ?>">

        <br>
        <!-- CAPTCHA -->
        <label for="captcha">Êtes-vous un robot ?</label>
        <input type="text" name="captcha" id="captcha"/>
		<img src="captcha.php" onclick="this.src='captcha.php?' + Math.random();" alt="captcha" style="cursor:pointer;">

        <input type="submit" value="Confirmer les modifications">
    </form>


    <?php
}
?>