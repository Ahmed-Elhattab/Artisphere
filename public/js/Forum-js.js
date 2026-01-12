let topics = JSON.parse(localStorage.getItem("topics")) || [];

function save() {
  localStorage.setItem("topics", JSON.stringify(topics));
}


// Filtre la liste
function filtrerSujets() {
  // On récupère ce qu'on a tapé
  const saisie = document.getElementById("inputRecherche").value.toLowerCase();
  
  const resultats = topics.filter(t => {
    // On vérifie si le champs de saisie n'est pas vide 
    return t.title.toLowerCase().includes(saisie);
  });

  // On rafraîchit l'affichage avec seulement les résultats
  render(resultats);
}


// On modifie le render (affichage) pour qu'il accepte la liste qu'on lui donne
function render(listeAAfficher = topics) {
const list = document.getElementById("topicsList");
const vide = document.getElementById("vide");

  list.innerHTML = "";

  if (listeAAfficher.length === 0) {
    vide.style.display = "block";
    return;
  }

  vide.style.display = "none";

listeAAfficher.forEach((t, index) => {
    list.innerHTML += `
      <div class="topic">
        <div class="topic-title">
          <a href="Forum-contenu-sujet.html?id=${index}" style="text-decoration:none; color:inherit; font-weight:bold;">
            ${t.title}
          </a>
        </div>
        <div class="topic-content">${t.content}</div>
      </div>
    `;
  });
}

//Trie la liste
function trierRecent() {
  render(topics); 
  document.getElementById("btn-recent").classList.add("active");
  document.getElementById("btn-ancien").classList.remove("active");
}

function trierAncien() {
  // On crée une copie et on l'inverse
  let anciens = [...topics].reverse();
  render(anciens);
  document.getElementById("btn-ancien").classList.add("active");
  document.getElementById("btn-recent").classList.remove("active");
}

//Ouvre la fenetre pour créer un sujet
function openFenetresujet() {
  document.getElementById("Fenetresujet").style.display = "flex";
}

//Ferme la fenetre pour créer un sujet
function closeFenetresujet() {
  document.getElementById("Fenetresujet").style.display = "none";
}

//Fonction de créer un sujet
function createTopic() {
  const title = document.getElementById("title").value.trim();
  const content = document.getElementById("content").value.trim();

  if (!title || !content) return alert("Veuillez remplir tous les champs.");

  topics.unshift({ title, content });
  save();
  closeFenetresujet();
  render();
}
render();


// 1. Récupération des données du sujet via l'URL
const urlParams = new URLSearchParams(window.location.search);
const id = urlParams.get('id');
const leSujet = topics[id];

// 2. Affichage au chargement
if (leSujet) {
    document.getElementById('titre').innerText = leSujet.title;
    document.getElementById('message').innerText = leSujet.content;
    chargerCommentaires();
}

// 3. FONCTION : Afficher les commentaires
function chargerCommentaires() {
    const liste = document.getElementById('liste-reponses');
    const topicsActuels = JSON.parse(localStorage.getItem("topics")) || [];
    const sujetActuel = topicsActuels[id];

    if (!sujetActuel || !sujetActuel.comments) return;

    liste.innerHTML = ""; 

    sujetActuel.comments.forEach(comment => {
        liste.innerHTML += `
            <div class="reponse-affichee">
                <img src="${comment.image || 'https://via.placeholder.com/40'}" class="reponse-auteur-img">
                <div class="reponse-contenu">
                    <span class="reponse-nom">${comment.nom || 'Anonyme'}</span>
                    <p class="reponse-texte">${comment.texte}</p>
                </div>
            </div>
        `;
    });
}

// 4. FONCTION : Ajouter une réponse
function ajouterCommentaire() {
    const champTexte = document.getElementById('comm-texte');
    const texte = champTexte.value.trim();

    if (texte === "") return;

    const user = JSON.parse(localStorage.getItem("user")) || {
        nom: "Utilisateur Test",
        image: "https://via.placeholder.com/40" 
    };

    const topicsMisAJour = JSON.parse(localStorage.getItem("topics")) || [];
    
    if (topicsMisAJour[id]) {
        if (!topicsMisAJour[id].comments) topicsMisAJour[id].comments = [];

        const nouvelleReponse = {
            texte: texte,
            nom: user.nom,
            image: user.image,
            date: new Date().toLocaleString()
        };

        topicsMisAJour[id].comments.push(nouvelleReponse);
        localStorage.setItem("topics", JSON.stringify(topicsMisAJour));

        champTexte.value = ""; 
        chargerCommentaires(); 
    }
  }

