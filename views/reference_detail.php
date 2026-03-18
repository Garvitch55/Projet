<?php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../head.php';

$title = "Détails de la référence";

// ----------------- Récupérer l'ID de la référence -----------------
$reference_id = $_GET['id'] ?? null;

if (!$reference_id) {
    header("Location: index.php?status=danger&message=" . urlencode("Référence introuvable."));
    exit;
}

$pdo = getPDO();
$stmt = $pdo->prepare("SELECT * FROM reference_management WHERE id = ?");
$stmt->execute([$reference_id]);
$reference = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$reference) {
    header("Location: index.php?status=danger&message=" . urlencode("Référence introuvable."));
    exit;
}

// ----------------- Construction du contenu -----------------
$content = <<<HTML
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="mb-4">

    <div class="d-flex justify-content-between align-items-center mt-3">
        <h1 class="text-orange-fonce">{$reference['name']}</h1>

        <a href="/projet/views/reference.php" class="btn text-white">
            <i class="bi bi-arrow-left me-2"></i> Retour
        </a>
    </div>
<h1 class="text-center text-danger"><i class="fa-solid fa-triangle-exclamation me-2 text-danger fa-beat"></i>Page en travaux</h1>
            </div>

            <div class="card mb-4 card-references">
HTML;

// Affichage de l'image si elle existe
if (!empty($reference['image']) && file_exists(__DIR__ . '/../assets/statics/images/' . $reference['image'])) {
    $content .= "
                <img src='/projet/assets/statics/images/{$reference['image']}' class='card-img-top rounded-1' alt='{$reference['name']}'>
    ";
}
$description_formatee = nl2br($reference['description']);

$content .= <<<HTML
                <div class="card-body">
                    <h5 class="card-title text-gris-fonce">Détails du projet</h5>
<p class="card-text">
    <strong>Lieu :</strong> {$reference['site']}<br>
    <strong>Description :</strong><br>
    {$description_formatee}<br>
    <strong>Date de réalisation :</strong> {$reference['Completion_date']}
</p>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
HTML;

// ----------------- Inclusion du layout -----------------
require __DIR__ . '/../layout.php';