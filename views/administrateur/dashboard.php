<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../head.php';    // head_with_title







// On récupère le type d'utilisateur depuis l'URL
$type = $_GET['type'] ?? 'administrateur';

// On capture le contenu spécifique pour le <main>
ob_start();
?>
<div class="container mt-1 border-bottom border-dark">
   <h6>Tableau de bord</h6>
</div>
<?php
// On stocke le contenu dans $content pour le layout
$content = ob_get_clean();

// On inclut le layout qui affichera navbar/sidebar et injectera $content dans <main>
require __DIR__ . '/../../layout.php';