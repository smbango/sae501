<?php
session_start();
if (!isset($_SESSION['user'])) { header("Location: index.php"); exit; }
?>
<!DOCTYPE html>
<html lang="fr" >
    <head>
        <meta charset="utf-8">
        <title>Liste des joueurs ETU 2025</title>
        <link href="style.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <p><a href="index.php">Retour au menu</a></p>
        <?php    
            include('fonctions.php');
            afficheFormEquipe();        

            if(!empty($_GET) && isset($_GET["equipe"])){  
                $numEquipe = $_GET['equipe']; 

                if($numEquipe == "toutes") {
                    echo "<h1> Voici la liste des joueurs de toutes les équipes </h1>";
                    $joueurs = listerjoueurs();        
                    if ($joueurs) afficheTableau($joueurs);    
                }
                else{
                    echo "<h1> Voici la liste des joueurs de l'équipe : ".getNomEquipeParNum($numEquipe)."</h1>";
                    $joueurs = listerjoueursParEquipe($numEquipe);        
                    if ($joueurs) afficheTableau($joueurs);    
                    else{
                        echo "<p>Erreur lors de la récupération des données depuis le formulaire</p>";
                    }    
                }
            }
        ?>
    </body>
</html>
