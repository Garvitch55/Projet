<?php
require_once __DIR__ . '/../../config.php';

// On récupère le type d'utilisateur depuis l'URL
$type = $_GET['type'] ?? 'administrateur';

// Pas connecté → dehors
if (!isset($_SESSION['id'])) {
    header("Location: login.php?status=danger&message=Veuillez vous connecter.");
    exit;
}

// Pas admin → dehors
if ($_SESSION['role'] !== 'administrateur') {
    header("Location: ../../index.php?status=danger&message=Accès refusé.");
    exit;
}

require_once __DIR__ . '/../../head.php';    // head_with_title
$title = "Factures";

// ----------------- CONTENT -----------------
$content = <<<HTML
<div class="mt-1 mb-4 border-bottom border-dark">
   <h6>Liste des factures</h6>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
HTML;

// ----------------- INCLURE LAYOUT -----------------
require __DIR__ . '/../../layout.php';
