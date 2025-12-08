<?php 

/**
 * Génère l'entête de la page avec un titre personnalisé
 * @param $title
 * @return void
 */
function head_with_title($title = "Accueil") {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <base href="http://localhost:8080/projet/">
        <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="node_modules/bootstrap-icons/font/bootstrap-icons.min.css">
        <script src="node_modules/bootstrap/dist/js/bootstrap.bundle.js"></script>
        <!-- On va linker notre CSS qui se trouve dans public/build -->
        <!-- LE HTML NE SAIT PAS LIRE LE SASS/SCSS, DONC TOUJOURS LINKER LE CSS -->
        <link rel="stylesheet" href="public/build/style.css">
        <title><?= $title ?></title>
    </head>
    <body>
    <?php 
}
?>