let topics = JSON.parse(localStorage.getItem("topics")) || [];

function save() {
  localStorage.setItem("topics", JSON.stringify(topics));
}


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
const realIndex = topics.indexOf(t); 
    list.innerHTML += `
      <div class="topic">
        <div class="topic-title">
          <a href="Forum_contenu_sujet.html?id=${realIndex}" style="text-decoration:none; color:inherit; font-weight:bold;">
            ${t.title}
          </a>
        </div>
        <div class="topic-content">${t.content}</div>
      </div>
    `;
  });
}

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

function openFenetresujet() {
  document.getElementById("Fenetresujet").style.display = "flex";
}

function closeFenetresujet() {
  document.getElementById("Fenetresujet").style.display = "none";
}

function createTopic() {
  const title = document.getElementById("title").value.trim();
  const content = document.getElementById("content").value.trim();

  if (!title || !content) return alert("Veuillez remplir tous les champs.");

  topics.unshift({ title, content });
  save();
  closeFenetresujet();
  render();

  document.getElementById("title").value = "";
  document.getElementById("content").value = "";
}

function chargerCommentaires() {
    const liste = document.getElementById('liste-reponses');
    if (!liste) return;

    // On recharge les topics depuis le storage pour avoir les derniers coms
    const topicsActuels = JSON.parse(localStorage.getItem("topics")) || [];
    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get('id');
    const sujet = topicsActuels[id];

    if (!sujet || !sujet.comments) return;

    // On vide la liste avant de la recréer
    liste.innerHTML = ""; 

    sujet.comments.forEach(comment => {
        liste.innerHTML += `
            <div class="reponse-affichee">
                <div class="reponse-contenu">
                    <span class="reponse-nom">Utilisateur</span>
                    <p class="reponse-texte">${comment.texte}</p>
                    <small style="color: #999; font-size: 12px;">${comment.date}</small>
                </div>
            </div>
        `;
    });
}

function ajouterCommentaire() {
    const champTexte = document.getElementById('comm-texte');
    const texte = champTexte.value.trim();

    if (texte === "") {
        alert("Vous ne pouvez pas envoyer une réponse vide !");
        return;
    }

    const topicsEnregistres = JSON.parse(localStorage.getItem("topics")) || [];
    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get('id');

    if (topicsEnregistres[id]) {
        if (!topicsEnregistres[id].comments) {
            topicsEnregistres[id].comments = [];
        }

        const nouvelleReponse = {
            texte: texte,
            date: new Date().toLocaleString('fr-FR')
        };
        
        topicsEnregistres[id].comments.push(nouvelleReponse);
        localStorage.setItem("topics", JSON.stringify(topicsEnregistres));

        champTexte.value = "";

        // On rafraîchit l'affichage sans recharger la page
        chargerCommentaires(); 
    }
}

// --- AFFICHAGE DU SUJET SUR LA PAGE DÉTAIL ---

// 1. On vérifie si on est bien sur la page du sujet (si les IDs titre et message existent)
const titreElem = document.getElementById('titre');
const messageElem = document.getElementById('message');

if (titreElem && messageElem) {
    // 2. On récupère l'ID depuis l'URL (?id=...)
    const urlParams = new URLSearchParams(window.location.search);
    const idSujet = urlParams.get('id');

    // 3. On récupère le sujet correspondant dans notre tableau "topics"
    // Note : "topics" est déjà déclaré tout en haut de ton fichier
    const leSujet = topics[idSujet];

    if (leSujet) {
        // 4. On remplit le bloc blanc avec les données
        titreElem.innerText = leSujet.title;
        messageElem.innerText = leSujet.content;
        
        // 5. On lance l'affichage des commentaires s'il y en a
        chargerCommentairesApresRemplissage();
    } else {
        titreElem.innerText = "Erreur";
        messageElem.innerText = "Sujet introuvable.";
    }
}

// Petite fonction pour afficher les commentaires déjà enregistrés
function chargerCommentairesApresRemplissage() {
    const listReponses = document.getElementById('liste-reponses'); // Assure-toi d'avoir cet ID dans ton HTML
    if (!listReponses) return;

    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get('id');
    const sujet = topics[id];

    if (sujet && sujet.comments) {
        listReponses.innerHTML = sujet.comments.map(c => `
            <div class="reponse-item">
                <p><strong>Anonyme</strong> <small>(${c.date})</small></p>
                <p>${c.texte}</p>
            </div>
        `).join('');
    }
}

// Ce bloc remplit automatiquement le titre et le contenu
window.onload = function() {
    // 1. On cherche si on est sur la page de détail
    const titreElem = document.getElementById('titre');
    const messageElem = document.getElementById('message');

    if (titreElem && messageElem) {
        const urlParams = new URLSearchParams(window.location.search);
        const id = urlParams.get('id');
        const leSujet = topics[id];

        if (leSujet) {
            titreElem.innerText = leSujet.title;
            messageElem.innerText = leSujet.content;
            chargerCommentaires(); // Affiche les réponses
        }
    } 
    // 2. Sinon, si on est sur la page d'accueil du forum
    else if (document.getElementById("topicsList")) {
        render();
    }
};