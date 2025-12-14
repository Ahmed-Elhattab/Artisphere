<!--<head>
  <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
</head> -->
<main class="container">
  <h1 class="page-title">AVIS PRODUITS</h1>
  <section class="card">
    <h2>Laisser une évaluation</h2>
    <div class="user-line">
      <span class="avatar">🧑‍🎨</span>
      <span class="username">User</span>
    </div>
    <hr>
    <div class="art-row">
      <div>Pot à lait en céramique</div>
      <img src="produit.png" alt="Oeuvre" class="art-thumb">
    </div>

    <!-- Étoiles interactives -->
    <div class="stars" role="radiogroup" aria-label="Note (de 1 à 5 étoiles)">
      <!-- data-value indique la valeur de l'étoile -->
      <button class="star" data-value="1" aria-label="1 étoile" aria-checked="false">★</button>
      <button class="star" data-value="2" aria-label="2 étoiles" aria-checked="false">★</button>
      <button class="star" data-value="3" aria-label="3 étoiles" aria-checked="false">★</button>
      <button class="star" data-value="4" aria-label="4 étoiles" aria-checked="false">★</button>
      <button class="star" data-value="5" aria-label="5 étoiles" aria-checked="true">★</button>
    </div>

    <!-- Champ caché pour la note sélectionnée -->
    <input type="hidden" id="rating" name="rating" value="5">

    <hr>
    <textarea id="avis" placeholder="Décrit ton avis ici ..." class="input"></textarea>
    <hr>
    <button class="btn" id="sendBtn">Envoyer</button>
  </section>
</main>