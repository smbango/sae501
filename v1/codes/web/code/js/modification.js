// MODIFICATION D'UN FILM

function getFormModFilm(){
    // récupère le formulaire de modification d'un film
    var idFilm = document.getElementById("choix-film");
	
	// 1. génération de l'URL en fonction de la saisie
	var req_AJAX = new XMLHttpRequest();
	
	// 2. initialisation de la requête AJAX
	if (req_AJAX) {
		req_AJAX.onreadystatechange = function() {
			traiterGetFormModFilm(req_AJAX); 
			};
			req_AJAX.open("POST", "php_ajax/ajax_modification.php", true);
		    req_AJAX.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			// 3. envoi de la requête AJAX
            req_AJAX.send("idFilm="+idFilm.value); 
    } else {
        alert("Pas de XMLHTTP !");
    }
}

function traiterGetFormModFilm(requete){
	// 4. traitement de la réponse du serveur à la requête

	var ready = requete.readyState; // récupère l'état de la requête
        if (ready == 4) { // si c'est fini
            if (requete.status == 200) { // code = 200 réponse HTTP OK
                var data = requete.responseText; // on récupère le "travail" du serveur
                document.getElementById("formulaire").innerHTML = data;
            } else {
                document.getElementById("formulaire").innerHTML = "erreur serveur, code " + requete.status;
            }
        }	
}

function getFormModRealisateur(){
    // récupère le formulaire de modification d'un film
    var idReal = document.getElementById("choix-real");
	
	// 1. génération de l'URL en fonction de la saisie
	var req_AJAX = new XMLHttpRequest();
	
	// 2. initialisation de la requête AJAX
	if (req_AJAX) {
		req_AJAX.onreadystatechange = function() {
			traiterGetFormModFilm(req_AJAX); 
			};
			req_AJAX.open("POST", "php_ajax/ajax_modification.php", true);
		    req_AJAX.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			// 3. envoi de la requête AJAX
            req_AJAX.send("idReal="+idReal.value); 
    } else {
        alert("Pas de XMLHTTP !");
    }
}

function traiterGetFormModRealisateur(requete ){
    // 4. traitement de la réponse du serveur à la requête

	var ready = requete.readyState; // récupère l'état de la requête
        if (ready == 4) { // si c'est fini
            if (requete.status == 200) { // code = 200 réponse HTTP OK
                var data = requete.responseText; // on récupère le "travail" du serveur
                document.getElementById("formulaire").innerHTML = data;
            } else {
                document.getElementById("formulaire").innerHTML = "erreur serveur, code " + requete.status;
            }
        }	
}
