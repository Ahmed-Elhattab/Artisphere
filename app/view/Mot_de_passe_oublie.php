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

<body id="Mot-de-passe-oublie">

<!--- CONTENU --->

<div class="container">
        <header class="form-header">
            <h1>RÉINITIALISATION DU MOT DE PASSE DU COMPTE</h1>
        </header>

        <form action="?controller=mot_de_passe_oublie&action=envoyer" method="POST">
            <div class="input-group">
                <label for="email">
                    Adresse courriel principale 
                     <span class="example-text">(nom@exemple.com)</span>
                </label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="button-container">
                <button type="submit" class="bouton-envoyer">
                    Envoyer la demande de réinitialisation du mot de passe par courriel
                </button>
            </div>
        </form>
    </div>

</body>
</html>