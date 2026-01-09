<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. On récupère l'email
    $email = htmlspecialchars($_POST['user_email']);

    // 2. On affiche l'alerte ET on redirige d'un coup avec JavaScript
    echo "<script>
            alert('Le courriel a bien été envoyé à : $email');
            window.location.href = 'Mot-de-passe-changer.html';
          </script>";
    
    exit;
}
?>