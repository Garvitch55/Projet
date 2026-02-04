<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../head.php';
require_once __DIR__ . '/../layout.php';

head_with_title("Accueil");

// Contenu spécifique à cette page
$content = <<<HTML
<h1>Bienvenue sur la page d'accueil</h1>
<p>Le contenu principal s’affiche ici.</p>
HTML;

// Inclut le layout (navbar + sidebar + main)
require __DIR__ . '/layout.php';
?>

