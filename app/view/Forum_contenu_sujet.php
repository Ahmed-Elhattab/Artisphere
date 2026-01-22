<!-- Contenue -->

<div id="Forum" class="forum-topic-page">

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
