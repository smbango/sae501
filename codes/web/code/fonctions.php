<?php
// ce fichier contient toutes les fonctions, afin de rendre le reste du code plus lisible et fluide.
// nous avons tous les deux travaillé dessus. un commentaire indiquera quelle fonction a été écrite par qui

    function authentification($mail, $pass) {
        // écrite par: Thomas LE FLOC'H (fortement inspiré de ce qui a été vu en TD/TP)
        $db = new PDO("sqlite:db/comptes.sqlite");

        // on sécurise les entrées utilisateur
        $mail = $db->quote($mail);
        $pass = $db->quote($pass);
        $sql = "SELECT EMAIL, STATUT FROM utilisateurs WHERE EMAIL=$mail AND PASS=$pass";

        $res = $db->query($sql);
        if ($res) {
            $tab = $res->fetch(PDO::FETCH_ASSOC);
        }
        else {
            echo "<p>erreur</p>";
        }

        return $tab;
    }

	function redirect($url,$tps) {
    // fonction fournie en TD par l'enseignant.
    // Intégrée dans ce code par Thomas LE FLOC'H pour utilisation dans index.php et connexion.php
		$temps = $tps * 1000;
		
		echo "<script type=\"text/javascript\">\n"
		. "<!--\n"
		. "\n"
		. "function redirect() {\n"
		. "window.location='" . $url . "'\n"
		. "}\n"
		. "setTimeout('redirect()','" . $temps ."');\n"
		. "\n"
		. "// -->\n"
		. "</script>\n";
	}

    function ajouter_log($message) {
        // Écrite par: Thomas LE FLOC'H
        // Écrit le message passé en paramètre dans logs/logins.log
        $fichierlog = fopen("logs/logins.log", "a+");
        fputs($fichierlog, $message);
        fputs($fichierlog, "\n");
        fclose($fichierlog);
    }


    function getFilmEtRealisateur() {
        // écrite par Thomas LE FLOC'H
        $db = new PDO("sqlite:db/DVDTheque.sqlite");
        // || est l'opérateur de concaténation en SQL
        $sql = "SELECT NoFilm AS Numéro, LienAffiche AS Affiche, Titre,DateSortie AS \"Date de sortie\",Genre,(Prenom || ' ' || Nom) AS Réalisateur
        FROM Film JOIN Realisateur ON Film.NoRealisateur = Realisateur.NoRealisateur;";
        $res = $db->query($sql);
        if ($res) {
            $tab = $res->fetchAll(PDO::FETCH_ASSOC);
        }
        return $tab;
    }


function afficheTableau($tab) {
    // fonction récupérée des TD
    // intégrée par Thomas LE FLOC'H
    echo '<table>';	
	echo '<tr>';// les entetes des colonnes qu'on lit dans le premier tableau par exemple
	foreach($tab[0] as $colonne=>$valeur){		
		echo "<th>$colonne</th>";		
	}
	echo "</tr>\n";
	// le corps de la table
	foreach($tab as $ligne){
		echo '<tr>';
		foreach($ligne as $cellule)	{
			echo "<td>$cellule</td>";		
		}
		echo "</tr>\n";
	}
	echo '</table>';
}
function afficheTableauAvecAffiche($tab){
    // inspirée de ce qui a été vu en TD, mais la fonction intègre les affiches de films
    // écrite par Thomas LE FLOC'H
    echo '<table>';	
    echo '<tr>';
    foreach($tab[0] as $colonne => $valeur){		
        echo "<th>$colonne</th>";		
    }
    echo "</tr>\n";

    // corps du tableau
    foreach($tab as $ligne){
        echo '<tr>';
        foreach($ligne as $colonne => $cellule){
            if ($colonne === 'Affiche') {
                echo "<td><img src=\"$cellule\" alt=\"Affiche\" style=\"max-height:250px;\"></td>";
            } else {
                echo "<td>$cellule</td>";
            }
        }
        echo "</tr>\n";
    }
    echo '</table>';
}


?>
