<?php
// Fichier contenant toutes les fonctions adaptées pour la V2 (MariaDB)

// Paramètres de connexion MariaDB
define('DB_HOST', 'db-v2');
define('DB_USER', 'root');
define('DB_PASS', 'L@nnion2026');

function authentification($mail, $pass) {
    try {
        $db = new PDO("mysql:host=" . DB_HOST . ";dbname=comptes;charset=utf8mb4", DB_USER, DB_PASS);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Sécurisation des entrées utilisateur
        $mail = $db->quote($mail);
        $pass = $db->quote($pass);
        $sql = "SELECT EMAIL, STATUT FROM utilisateurs WHERE EMAIL=$mail AND PASS=$pass";

        $res = $db->query($sql);
        $tab = $res ? $res->fetch(PDO::FETCH_ASSOC) : false;
        return $tab;
    } catch (PDOException $e) {
        die("<p>Erreur d'authentification : " . $e->getMessage() . "</p>");
    }
}

function redirect($url, $tps) {
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
    $fichierlog = fopen("logs/logins.log", "a+");
    fputs($fichierlog, $message . "\n");
    fclose($fichierlog);
}

function getFilmEtRealisateur() {
    try {
        $db = new PDO("mysql:host=" . DB_HOST . ";dbname=dvdtheque;charset=utf8mb4", DB_USER, DB_PASS);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Utilisation de CONCAT() pour MariaDB au lieu de l'opérateur SQLite ||
        $sql = "SELECT NoFilm AS Numéro, LienAffiche AS Affiche, Titre, DateSortie AS `Date de sortie`, Genre, CONCAT(Prenom, ' ', Nom) AS Réalisateur
                FROM Film 
                JOIN Realisateur ON Film.NoRealisateur = Realisateur.NoRealisateur;";
        
        $res = $db->query($sql);
        return $res ? $res->fetchAll(PDO::FETCH_ASSOC) : [];
    } catch (PDOException $e) {
        die("<p>Erreur lors de la récupération des films : " . $e->getMessage() . "</p>");
    }
}

function afficheTableau($tab) {
    if (empty($tab)) {
        echo "<p>Aucune donnée à afficher.</p>";
        return;
    }
    echo '<table>'; 
    echo '<tr>';
    foreach ($tab[0] as $colonne => $valeur) {      
        echo "<th>$colonne</th>";       
    }
    echo "</tr>\n";
    foreach ($tab as $ligne) {
        echo '<tr>';
        foreach ($ligne as $cellule) {
            echo "<td>$cellule</td>";       
        }
        echo "</tr>\n";
    }
    echo '</table>';
}

function afficheTableauAvecAffiche($tab) {
    if (empty($tab)) {
        echo "<p>Aucun film trouvé.</p>";
        return;
    }
    echo '<table>'; 
    echo '<tr>';
    foreach ($tab[0] as $colonne => $valeur) {        
        echo "<th>$colonne</th>";       
    }
    echo "</tr>\n";

    foreach ($tab as $ligne) {
        echo '<tr>';
        foreach ($ligne as $colonne => $cellule) {
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
