<?php

// $_GET est une superglobale qui sert à récuperer les variables dans l'URL
// C'est variable sont TOUJOURS après le "?"
if (isset($_GET['status']) && isset($_GET['message'])) {
    $status = $_GET['status'];
    $message = $_GET['message'];
    ?>
    <!-- Le htmlentities permet de contrer les attaques xss 
    (Cross-Site-Scripting) -->
    <!-- Càd dans le cas ci-dessous d'écrire un script js 
    directement dans le message -->
    <!-- Ce qui permettrait à une personne malveillante et 
    compétante, de hacker votre site  -->
    <!-- Le htmlentities "sanitize" la chaine de caractère 
     du script malveillant 
    et la transforme en texte inoffensif en un texte -->
    <p class="notification notification-<?= htmlentities($status) ?> text-<?= htmlentities($status) ?>"><?= htmlentities($message) ?></p>
    
<?php
}