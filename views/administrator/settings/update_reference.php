<?php
// settings/update_reference.php

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

$title = "Modifier une référence";

// ----------------- Vérifier que l'ID est fourni -----------------
$reference_id = $_GET['id'] ?? null;
if (!$reference_id) {
    header("Location: list_references.php?status=danger&message=" . urlencode("Référence introuvable."));
    exit;
}

// ----------------- Récupérer les données existantes -----------------
$pdo = getPDO();
$stmt = $pdo->prepare("SELECT * FROM reference_management WHERE id = ?");
$stmt->execute([$reference_id]);
$reference = $stmt->fetch();

if (!$reference) {
    header("Location: list_references.php?status=danger&message=" . urlencode("Référence introuvable."));
    exit;
}

// ----------------- Capturer la notification -----------------
ob_start();
require ROOT . "notification.php";
$notification = ob_get_clean();

// ----------------- Préparer la date pour le champ date -----------------
$value_date = !empty($reference['Completion_date']) && $reference['Completion_date'] != '0000-00-00'
    ? $reference['Completion_date']
    : '';

// ----------------- Construction du contenu -----------------
$content = <<<HTML
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h1 class="h3 mb-3 text-center">Modifier la référence</h1>

            <!-- Formulaire -->
            <form method="POST" enctype="multipart/form-data" action="controller/reference/update_reference_ctrl.php">
                <input type="hidden" name="reference_id" value="{$reference['id']}">

                <div class="mb-3">
                    <label for="reference_name" class="form-label text-orange-fonce">Nom de la référence : <span class="text-danger">*</span></label>
                    <input type="text" name="reference_name" id="reference_name" class="form-control" value="{$reference['name']}" required>
                </div>

                <div class="mb-3">
                    <label for="reference_description" class="form-label text-orange-fonce">Description : <span class="text-danger">*</span></label>
                    <textarea name="reference_description" id="reference_description" class="form-control">{$reference['description']}</textarea>
                </div>

                <div class="mb-3">
                    <label for="completion_date" class="form-label text-orange-fonce">Date de réalisation : <span class="text-danger">*</span></label>
                    <input type="date" name="completion_date" id="completion_date" class="form-control" value="{$value_date}" required>
                </div>

                <div class="mb-3">
                    <label for="site" class="form-label text-orange-fonce">Lieu : <span class="text-danger">*</span></label>
                    <input type="text" name="site" id="site" class="form-control" value="{$reference['site']}">
                </div>

                <div class="mb-3">
                    <label for="reference_image" class="form-label text-orange-fonce">Image :</label>
                    <input type="file" name="reference_image" id="reference_image" class="form-control" accept=".jpg,.jpeg,.png,.gif">
HTML;

// J'affiche l'image actuelle si elle existe
if (!empty($reference['image']) && file_exists(__DIR__ . '/../../../images/' . $reference['image'])) {
    $content .= "<p class='mt-2 text-orange-fonce'>Image actuelle : <img src='/projet/images/{$reference['image']}' alt='Image référence' style='max-width:200px;'></p>";
}

$content .= <<<HTML
                </div>

                <div class="text-center">
                    <button type="submit" class="btn text-white">Modifier la référence</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
HTML;

// ----------------- Inclusion du layout -----------------
require ROOT . 'layout.php';