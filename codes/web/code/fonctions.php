<?php
// Connexion à la base métier rugby
function connexionBD() {
    try {
        $madb = new PDO('sqlite:/var/data/data.db');
        $madb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } 
    catch (PDOException $e) {
        echo "Connection impossible : ", $e->getMessage();
        die();
    }  
    return $madb;
}

// Connexion à la base d'authentification des utilisateurs
function connexionAuthBD() {
    try {
        $authdb = new PDO('sqlite:/var/data/auth.db');
        $authdb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } 
    catch (PDOException $e) {
        echo "Connection impossible auth : ", $e->getMessage();
        die();
    }  
    return $authdb;
}

// Affichage du tableau HTML
function afficheTableau($tab){
    if (empty($tab)) {
        echo "<p>Aucun résultat pour cette sélection.</p>";
        return;
    }
    echo '<table>';    
    echo '<tr>';
    foreach($tab[0] as $colonne=>$valeur){
        echo "<th>$colonne</th>";
    }
    echo "</tr>\n";
    foreach($tab as $ligne){
        echo '<tr>';
        foreach($ligne as $cellule) {
            echo "<td>". htmlspecialchars($cellule) ."</td>";
        }
        echo "</tr>\n";
    }
    echo '</table>';
}

// EX1 : Lister tous les joueurs
function listerjoueurs() {
    $retour = false;
    try {  
        $connBD = connexionBD();
        $req = "SELECT j.nom_joueur as Joueur, e.nom_equipe as Equipe 
                FROM joueurs j 
                JOIN equipes e ON e.num_equipe = j.num_equipe 
                ORDER BY e.nom_equipe, j.nom_joueur";
        $results = $connBD->query($req);
        if ($results) {
            $retour = $results->fetchAll(PDO::FETCH_ASSOC);
        }
    } 
    catch (PDOException $e) {
        echo 'Erreur listerjoueurs : ' . $e->getMessage();
    }
    return $retour;
}

// EX2 : Formulaire de choix d'équipe
function afficheFormEquipe() {
    try {  
        $connBD = connexionBD();
        $req = "SELECT DISTINCT e.num_equipe, e.nom_equipe FROM equipes e INNER JOIN joueurs j ON e.num_equipe = j.num_equipe";
        $results = $connBD->query($req);
        $tab_equipes = $results ? $results->fetchAll(PDO::FETCH_ASSOC) : [];
    } 
    catch (PDOException $e) {
        echo 'Erreur afficheFormEquipe : ' . $e->getMessage();
        $tab_equipes = [];
    }    
    $selectedEquipe = $_GET['equipe'] ?? '';
    ?>
    <h1>Sélectionner une Equipe pour afficher la liste des joueurs</h1>
    <form action="EX2_listejoueursParEquipe.php" method="get">
        <fieldset>
            <select name="equipe">
                <option value="toutes" <?= $selectedEquipe === 'toutes' ? 'selected' : '' ?>>Toutes les Equipes</option>
                <?php
                    foreach($tab_equipes as $equipe){
                        $sel = ($selectedEquipe == $equipe["num_equipe"]) ? 'selected' : '';
                        echo '<option value="'.$equipe["num_equipe"] .'" '.$sel.'>'.$equipe["nom_equipe"].'</option>';
                    }
                ?>
            </select>
            <input type="submit" value="Afficher les joueurs"/>
        </fieldset>
    </form>
    <?php
}

// EX2 : Lister les joueurs d'une équipe
function listerjoueursParEquipe($numEquipe) {
    $retour = false;
    try {    
        $connBD = connexionBD();    
        $stmt = $connBD->prepare("SELECT j.nom_joueur as Joueur, e.nom_equipe as Equipe 
                                  FROM joueurs j 
                                  JOIN equipes e ON e.num_equipe = j.num_equipe 
                                  WHERE j.num_equipe = :ne 
                                  ORDER BY j.nom_joueur");
        $stmt->execute(['ne' => (int)$numEquipe]);
        $retour = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } 
    catch (PDOException $e) {
        echo "Erreur listerjoueursParEquipe : ", $e->getMessage();        
    }
    return $retour;
}

// EX2 : Récupérer le nom de l'équipe
function getNomEquipeParNum($numEquipe) {
    $retour = "Inconnue";
    try {  
        $connBD = connexionBD();
        $stmt = $connBD->prepare("SELECT nom_equipe FROM equipes WHERE num_equipe = :ne");
        $stmt->execute(['ne' => (int)$numEquipe]);
        $tab = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($tab) {
            $retour = $tab["nom_equipe"];
        }
    } 
    catch (PDOException $e) {
        echo 'Erreur nom equipe : ' . $e->getMessage();
    }        
    return $retour;
}

// EX3 : Formulaire d'insertion
function afficheFormulaireAjoutjoueur(){
    ?>
    <h1>Insertion d'un nouveau joueur</h1>        
    <form action="EX3_InsererJoueurs.php" method="post">
        <fieldset> 
            <label for="id_joueur">Nom joueur : </label>
            <input type="text" name="joueur" id="id_joueur" size="20" required /><br />
            <label for="id_equipe">Nom equipe : </label>
            <select name="equipe" id="id_equipe" required>
                <?php
                try {  
                    $connBD = connexionBD();
                    $req = "SELECT DISTINCT nom_equipe, num_equipe FROM equipes";
                    $Equipe = $connBD->query($req);
                    if ($Equipe) {
                        $tab = $Equipe->fetchAll();    
                        foreach($tab as $value) {
                            echo '<option value="'.$value['num_equipe'].'">'. $value['nom_equipe'].'</option>';        
                        }
                    }
                } 
                catch (PDOException $e) {
                    echo 'Erreur formulaire ajout : ' . $e->getMessage();
                }      
                ?>
            </select><br />
            <input type="submit" value="Insérer"/>
        </fieldset>
    </form>
    <?php
}

// EX3 : Insertion d'un joueur
function insererjoueur($joueur, $num_equipe){
    $sortie = false;
    try {    
        $connBD = connexionBD();
        $stmt = $connBD->prepare("SELECT nom_joueur FROM joueurs WHERE nom_joueur = :nom AND num_equipe = :eq");
        $stmt->execute(['nom' => trim($joueur), 'eq' => (int)$num_equipe]);
        $tab = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (!empty($tab)) {
            $sortie = "Le joueur existe déjà";
        } else {
            $maxRes = $connBD->query("SELECT MAX(num_joueur) AS max_id FROM joueurs")->fetch(PDO::FETCH_ASSOC);
            $nextId = ($maxRes['max_id'] ?? 0) + 1;
            
            $insert = $connBD->prepare("INSERT INTO joueurs (num_joueur, nom_joueur, num_equipe) VALUES (:id, :nom, :eq)");
            $insert->execute(['id' => $nextId, 'nom' => trim($joueur), 'eq' => (int)$num_equipe]);
            $sortie = "Insertion réussie du joueur : " . htmlspecialchars($joueur);
        }        
    }    
    catch (Exception $e) {        
        echo "Erreur insererjoueur : " . $e->getMessage();        
    }
    return $sortie;
}

// Authentification
function verifierConnexion($u, $p) {
    try {
        $db = connexionAuthBD();
        $stmt = $db->prepare("SELECT * FROM users WHERE username = :u AND password = :p");
        $stmt->execute(['u' => $u, 'p' => $p]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return false;
    }
}
?>
