let topics = JSON.parse(localStorage.getItem("topics")) || [];

function save() {
  localStorage.setItem("topics", JSON.stringify(topics));
}


// On modifie le render pour qu'il accepte la liste qu'on lui donne
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

render();