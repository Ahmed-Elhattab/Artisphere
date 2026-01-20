<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="styles_Thushjan.css">
    <meta charset="UTF-8">
    <title>Front</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <link rel="stylesheet" href="header.css">
    <title>Document</title>

<body id="Forum_contenu_sujet">


<!-- Contenue -->

<div id="Forum">
    <a href="/artisphere/?controller=forum&action=index">Retour au forum</a>
    
    <div class="boite-sujet">
        <h1 id="titre"></h1>
        <p id="message"></p>
    </div>

    <div class="commentaires">
        <h3>Réponses</h3>
         <div id="liste-reponses"></div>
        <textarea id="comm-texte" placeholder="Écrire une réponse..."></textarea>
        <button onclick="ajouterCommentaire()">Répondre</button> <!-- Fonction Ajoutercommentaire -->
    </div>
</div>

<script src="js/Forum-js.js"></script>

</body>
</html>