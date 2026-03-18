<?php

session_start();

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../../head.php';

$title = "Créer une TVA";

ob_start();
require ROOT . "notification.php";
$notification = ob_get_clean();

$content = <<<HTML

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="text-orange-fonce m-0">Ajouter une TVA</h1>
                <a href="/projet/views/administrator/settings/list_tva.php" class="btn text-white"><i class="bi bi-arrow-left me-2"></i>Retour</a>
            </div>

            <form method="POST" action="/projet/controller/administrator/create_tva_ctrl.php">
                <div class="mb-3">
                    <label class="form-label text-gris-fonce">Nom de la TVA: <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label text-gris-fonce">Taux (%): <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="rate" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label text-gris-fonce">Description</label>
                    <textarea name="description" class="form-control"></textarea>
                </div>
                <div class="d-flex justify-content-center mt-3">
                    <button type="submit" class="btn text-white">Créer la TVA</button>
                </div>
            </form>
        </div>
    </div>
</div>

HTML;

require ROOT . 'layout.php';