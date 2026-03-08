<?php
// settings/list_references.php

session_start();

// ----------------- Vérification de connexion -----------------
if (!isset($_SESSION['id'])) {
    header("Location: login.php?status=danger&message=" . urlencode("Veuillez vous connecter."));
    exit;
}

// ----------------- Vérification du rôle -----------------
if ($_SESSION['role'] !== 'administrateur') {
    header("Location: ../../../index.php?status=danger&message=" . urlencode("Accès refusé."));
    exit;
}

// ----------------- Inclure config et head -----------------
require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../../head.php';

$title = "Liste des références";

// ----------------- Récupération des références -----------------
$pdo = getPDO();
$stmt = $pdo->query("SELECT * FROM reference_management ORDER BY Completion_date DESC");
$references = $stmt->fetchAll();

// ----------------- Capturer la notification -----------------
ob_start();
require ROOT . "notification.php";
$notification = ob_get_clean();

// ----------------- Construction du contenu -----------------
$content = <<<HTML
<div class="container mt-5">
    <h1 class="text-center mb-4">Liste des références</h1>

    <!-- Notification -->
    {$notification}

    <div class="row">
HTML;

// ----------------- Boucle sur les références -----------------
foreach ($references as $ref) {

    $cards = htmlspecialchars($ref['name']);
    $image = htmlspecialchars($ref['image']);
    $site = htmlspecialchars($ref['site']);
    $description = nl2br(htmlspecialchars($ref['description']));
    $completion_date = htmlspecialchars($ref['Completion_date']);
    $id = urlencode($ref['id']);

    $content .= "
        <div class='col-md-4 mb-4'>
            <div class='card h-100 shadow-sm text-white' style='border:1px solid #e38f3c; background: rgba(227, 143, 60, 0.6);'>
                
                <!-- Image -->
                <img src='/projet/images/{$image}' class='card-img-top' alt='{$cards}'>

                <div class='card-body'>
                    <!-- Nom -->
                    <h5 class='card-title'>{$cards}</h5>

                    <!-- Site et Description -->
                    <p class='card-text'>
                        Lieu: {$site}<br>
                        Description: {$description}<br>
                        Date de réalisation: {$completion_date}<br>
                    </p>

                    <!-- Bouton Modifier -->
                    <a href='views/administrator/settings/update_reference.php?id={$id}' class='btn text-white'>
                        Modifier la référence
                    </a>
                </div>
            </div>
        </div>
    ";
}

$content .= <<<HTML
    </div> <!-- row -->
</div> <!-- container -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
HTML;

// ----------------- Inclusion du layout -----------------
require ROOT . 'layout.php';