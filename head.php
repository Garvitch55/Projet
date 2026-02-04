<?php
function head_with_title($title = "Accueil") {
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="http://localhost/projet/">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="node_modules/bootstrap-icons/font/bootstrap-icons.min.css">

    <!-- CSS projet -->
    <link rel="stylesheet" href="public/build/style.css">

    <title><?= $title ?></title>
</head>
<body>
<?php
}
?>