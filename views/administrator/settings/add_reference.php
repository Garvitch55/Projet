<?php

session_start();

// ----------------- Vérification de connexion -----------------
// Pas connecté → dehors
if (!isset($_SESSION['id'])) {
    header("Location: login.php?status=danger&message=" . urlencode("Veuillez vous connecter."));
    exit;
}

// ----------------- Vérification du rôle -----------------
// Pas admin → dehors
if ($_SESSION['role'] !== 'administrateur') {
    header("Location: ../../../index.php?status=danger&message=" . urlencode("Accès refusé."));
    exit;
}

// ----------------- Inclure config et head -----------------
require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../../head.php';

$title = "Ajouter une référence";

// ----------------- Capture de la notification -----------------
ob_start();
require ROOT . "notification.php";
$notification = ob_get_clean();

// ----------------- Construction du contenu -----------------
$content = <<<HTML
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h1 class="h3 mb-3 text-center">Ajouter une référence</h1>

            <!-- ----------------- Formulaire ----------------- -->
           <form method="POST" enctype="multipart/form-data" action="controller/reference/add_reference_ctrl.php">

                <div class="mb-3">
                    <label for="reference_name" class="form-label text-orange-fonce">Nom de la référence : <span class="text-danger">*</span></label>
                    <input type="text" name="reference_name" id="reference_name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="reference_description" class="form-label text-orange-fonce">Description : <span class="text-danger">*</span></label>
                    <textarea name="reference_description" id="reference_description" class="form-control"></textarea>
                </div>

                <div class="mb-3">
                    <label for="completion_date" class="form-label text-orange-fonce">Date de réalisation : <span class="text-danger">*</span></label>
                    <input type="date" name="completion_date" id="completion_date" class="form-control" required>
                </div>

<div class="mb-3">
    <label for="reference_site" class="form-label text-orange-fonce">Lieu : <span class="text-danger">*</span></label>
    <input type="text" name="reference_site" id="site" class="form-control" class="form-control" required>
</div>

                <div class="mb-3">
                    <label for="reference_image" class="form-label text-orange-fonce">Image : <span class="text-danger">*</span></label>
                    <input type="file" name="reference_image" id="reference_image" class="form-control" accept=".jpg,.jpeg,.png,.gif">
                </div>

                <div class="text-center">
                    <button type="submit" class="btn text-white">Ajouter la référence</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
HTML;

// ----------------- Inclusion du layout -----------------
require ROOT . 'layout.php';