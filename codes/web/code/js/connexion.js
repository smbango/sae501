function checkPassword() {
    // vérifie si le mot de passe est assez solide. sinon, bloque l'envoi du formulaire de connexion
    // écrite par: Thomas LE FLOC'H
    var retour = false;

    var mdp = document.getElementById("pass");
    var reg =/^(?=.*[A-Z])(?=.*[^a-zA-Z0-9]).+$/;   // regex qui teste nos contraintes sur le mot de passe (au moins 1 majuscule & 1 caractère spécial)

    if (mdp.value.length != 0) {
        if (reg.test(mdp.value)) {
            retour = true;
        } else {
            document.getElementById("mdp-err").innerHTML = "Mot de passe trop faible.";
        }
    }
    else {
        document.getElementById("mdp-err").innerHTML = "Il faut saisir un mot de passe.";
    }

    return retour;
}
