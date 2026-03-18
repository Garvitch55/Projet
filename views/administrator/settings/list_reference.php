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
<section class="m-4">
    <div class="d-flex justify-content-between align-items-center mt-3">
        <h1 class="text-orange-fonce mb-4">Liste des références pour modification(s)</h1>

        <a href="/projet/views/administrator/parameter.php" class="btn text-white">
            <i class="bi bi-arrow-left me-2"></i> Retour
        </a>
    </div>
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
            <div class='card h-100 card-references position-relative'>
                <a href='#' 
                   class='btn3 btn-sm position-absolute top-0 end-0 m-2 rounded-circle d-flex align-items-center justify-content-center'
                   style='width:35px;height:35px;'
                   data-bs-toggle='modal'
                   data-bs-target='#deleteModal{$id}'>
                   <i class='bi bi-x-lg'></i>
                </a>

                <!-- Image -->
                <img src='/projet/assets/statics/images/{$image}' class='card-img-top' alt='{$cards}'>

                <div class='card-body'>
                    <!-- Nom -->
                    <h5 class='card-title text-gris-fonce'>{$cards}</h5>

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

        <!-- Modal de suppression -->
        <div class='modal fade' id='deleteModal{$id}' tabindex='-1'>
            <div class='modal-dialog modal-dialog-centered'>
                <div class='modal-content'>
                    <div class='modal-header bg-gris-fonce text-white rounded-1'>
                        <h5 class='modal-title'>Confirmation</h5>
                        <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                    </div>

                    <div class='modal-body'>
                        Êtes-vous sûr de vouloir supprimer cette référence ?
                    </div>

                    <div class='modal-footer'>
                        <button type='button' class='btn text-white' data-bs-dismiss='modal'>Annuler</button>
                        <a href='controller/reference/delete_reference_ctrl.php?id={$id}' class='btn text-white'>
                            Supprimer
                        </a>
                    </div>
                </div>
            </div>
        </div>
    ";
}

$content .= <<<HTML
    </div> <!-- row -->
</section> <!-- fermeture de la section -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
HTML;

// ----------------- Inclusion du layout -----------------
require ROOT . 'layout.php';