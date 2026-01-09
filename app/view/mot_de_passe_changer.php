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

<body id="Mot-de-passe-changer">


<!-- CONTENU -->

<div class="container">
    <header class="form-header">
        <h1>CHOISIR UN NOUVEAU MOT DE PASSE</h1>
    </header>

    <form>
        <p class="instruction">Veuillez saisir votre nouveau mot de passe ci-dessous.</p>

        <div class="input-group">
            <label for="new-password">Nouveau mot de passe *</label>
            <input type="password" id="new-password" name="new-password" required>
        </div>

        <div class="input-group">
            <label for="confirm-password">Confirmez le nouveau mot de passe *</label>
            <input type="password" id="confirm-password" name="confirm-password" required>
        </div>

        <div class="button-container">
            <button type="submit" class="bouton-envoyer">
                Enregistrer le nouveau mot de passe
            </button>
        </div>
    </form>
</div>

<script src="../Artisphere/public/js/accessibilite.js"></script>
</body>
</html>
