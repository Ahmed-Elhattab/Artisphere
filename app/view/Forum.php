<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Front</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <link rel="stylesheet" href="header.css">
    <link rel="stylesheet" href="../../public/css/styles_Thushjan.css">
    <title>Document</title>
</head>
    
<body id="Forum">

<!-- Contenue -->

<header class="top">
  <div class="container">
    <div class="search-container">
    <h1>Forum Communautaire</h1>
    <div class="search-box">
      <input type="text" id="inputRecherche" placeholder="Recherche" oninput="filtrerSujets()">
      <button>🔍</button>
    </div>
   <img src="images/Communauté.png" class="image-communauté"/>
   </div>
  </div>
</header>


<div class="barre"></div>

<main class="container forum-layout">
  <section class="topics">

    <div class="toolbar">
      <div class="filtres">
        <button id="btn-recent" class="active" onclick="trierRecent()">Récents</button>
        <button id="btn-ancien" onclick="trierAncien()">Anciens</button>
      </div>
      <button class="new-topic" onclick="openFenetresujet()">➕ Créer un Topic</button> <!-- Ouvre une fenetre de création de sujet -->
    </div>

<!-- Aucun sujet -->
    <div id="vide" class="vide">
      Aucun sujet pour l'instant. Lancez le premier 🚀
    </div>

<!-- Plusieurs sujet -->
    <div id="topicsList"></div>
  </section>

<!-- Sidebar -->
  <aside class="sidebar">
    <h3>Communauté</h3>
    <p>Lancez des sujets, partagez vos idées et aidez la communauté.</p>
  </aside>

</main>

<!-- Fenetre-sujet -->
<div id="Fenetresujet" class="Fenetresujet">
  <div class="Fenetresujet-contenu">
    <h2>Créer un sujet de discussion</h2>
    <input id="title" placeholder="Titre du sujet">
    <textarea id="content" placeholder="Ecrit ton message..."></textarea>
    <div class="Fenetresujet-actions">
      <button onclick="closeFenetresujet()">Annuler</button>
      <button onclick="createTopic()">Publier</button>
    </div>
  </div>
</div>


<script src="js/Forum-js.js"></script>

</body>
</html>