const searchForm = document.getElementById('searchForm');
const searchInput = document.getElementById('searchInput'); 
const resultsArea = document.getElementById('resultsArea'); 



searchForm.addEventListener('submit', function(evenement) {
    
    evenement.preventDefault(); 

    const nom = searchInput.value.trim(); 
    
    if (nom !== "") { 
        creerBarreUtilisateur(nom); 
        searchInput.value = "";
    }
});


function creerBarreUtilisateur(nom) {
    
    const nouvelleBarre = document.createElement('div');
    nouvelleBarre.className = 'barre-grise';
    nouvelleBarre.innerHTML = `
        <span class="profile-name">${nom.toUpperCase()}</span>
        <span class="account-type">COMPTE ARTISAN</span>
        <div>
            <button class="status-light light-green"></button>
            <button class="status-light light-red"></button>
        </div>
    `;

//Si on appuie sur le bouton vert
nouvelleBarre.querySelector('.light-green').onclick = function() {
    
    // On demande confirmation avant de donner des droits
    if (confirm("Voulez-vous nommer " + nom + " administrateur ?")) {
        
        // On envoie l'ordre au serveur
        fetch('modifier_statut.php', {
            method: 'POST', // On utilise POST pour envoyer des données
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'nom=' + encodeURIComponent(nom) + '&role=admin'
        })
        .then(reponse => {
            if (reponse.ok) {
                // Si la base de données est mise à jour, on change l'affichage sur l'écran
                nouvelleBarre.querySelector('.account-type').innerText = "ADMINISTRATEUR";
                alert(nom + " est maintenant administrateur en base de données !");
            } else {
                alert("Erreur serveur : impossible de modifier le rôle.");
            }
        })
        .catch(erreur => alert("Erreur de connexion au serveur."));
    }
};

//Si on appuie sur le bouton rouge
nouvelleBarre.querySelector('.light-red').onclick = function() {
    if (confirm("Supprimer définitivement de la base de données ?")) {
        
        // On envoie une requête au serveur (le fichier 'supprimer.php' ou une API)
        fetch('supprimer_utilisateur.php?nom=' + nom, {
            method: 'DELETE' // On utilise la méthode de suppression
        })
        .then(reponse => {
            if (reponse.ok) {
                // SEULEMENT si le serveur confirme que c'est supprimé en base,
                // alors on retire la barre de l'écran
                nouvelleBarre.remove();
                alert("Compte supprimé avec succès !");
            } else {
                alert("Erreur : le serveur n'a pas pu supprimer le compte.");
            }
        });
    }
};

    resultsArea.innerHTML = ""; 
    resultsArea.appendChild(nouvelleBarre); 
}