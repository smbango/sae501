<?php
session_start();
include_once "../formulaires.php";
// ce fichier sert à ne renvoyer que les données utiles à AJAX


if (isset($_POST) && isset($_POST["idFilm"])) { // le film à modifier a été choisi
    formulaire_modification_film();
}
else if (isset($_POST) && isset($_POST["idReal"])) {    // cas où on a choisi un réalisateur à modifier
    formulaire_modification_realisateur();
}


?>