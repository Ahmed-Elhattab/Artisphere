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


function ajouterCommentaire() {
    // 1. Récupérer le champ texte et son contenu
    const champTexte = document.getElementById('comm-texte');
    const texte = champTexte.value.trim();

    // 2. Vérifier que le message n'est pas vide
    if (texte === "") {
        alert("Vous ne pouvez pas envoyer une réponse vide !");
        return;
    }

    // 3. Charger les données actuelles depuis le localStorage
    const topics = JSON.parse(localStorage.getItem("topics")) || [];
    
    // On récupère l'id depuis l'URL (défini au début de ton script)
    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get('id');

    if (topics[id]) {
        // 4. Initialiser le tableau de commentaires s'il n'existe pas encore
        if (!topics[id].comments) {
            topics[id].comments = [];
        }

        // 5. Ajouter la nouvelle réponse
        // On peut ajouter un objet avec la date pour faire plus pro
        const nouvelleReponse = {
            texte: texte,
            date: new Date().toLocaleString('fr-FR')
        };
        
        topics[id].comments.push(nouvelleReponse);

        // 6. Sauvegarder les modifications dans le localStorage
        localStorage.setItem("topics", JSON.stringify(topics));

        // 7. Effacer le champ de saisie
        champTexte.value = "";

        // 8. Rafraîchir l'affichage des commentaires
        location.reload(); 
    }
}
