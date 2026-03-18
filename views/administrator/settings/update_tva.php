<?php

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

$title = "Modifier une TVA";

// ----------------- Vérifier que l'ID est fourni -----------------
$tva_id = $_GET['id'] ?? null;

if (!$tva_id) {
    header("Location: list_tva.php?status=danger&message=" . urlencode("TVA introuvable."));
    exit;
}

// ----------------- Récupérer les données existantes -----------------
$pdo = getPDO();

$stmt = $pdo->prepare("
    SELECT *
    FROM tva
    WHERE id_tva = ?
");

$stmt->execute([$tva_id]);

$tva = $stmt->fetch();

if (!$tva) {
    header("Location: list_tva.php?status=danger&message=" . urlencode("TVA introuvable."));
    exit;
}

// ----------------- Capturer la notification -----------------
ob_start();
require ROOT . "notification.php";
$notification = ob_get_clean();

// ----------------- Construction du contenu -----------------

$content = <<<HTML

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-6">

            <div class="d-flex justify-content-between align-items-center mb-3">

                <h1 class="text-orange-fonce m-0">
                    Modifier la TVA
                </h1>

                <a href="/projet/views/administrator/settings/list_tva.php"
                   class="btn text-white">

                    <i class="bi bi-arrow-left me-2"></i>
                    Retour

                </a>

            </div>


            <form method="POST"
                  action="/projet/controller/administrator/update_tva_ctrl.php">

                <input type="hidden"
                       name="id_tva"
                       value="{$tva['id_tva']}">


                <div class="mb-3">

                    <label for="name"
                           class="form-label text-gris-fonce">

                        Nom de la TVA :
                        <span class="text-danger">*</span>

                    </label>

                    <input type="text"
                           name="name"
                           id="name"
                           class="form-control"
                           value="{$tva['name']}"
                           required>

                </div>


                <div class="mb-3">

                    <label for="rate"
                           class="form-label text-gris-fonce">

                        Taux (%):
                        <span class="text-danger">*</span>

                    </label>

                    <input type="number"
                           step="0.01"
                           name="rate"
                           id="rate"
                           class="form-control"
                           value="{$tva['rate']}"
                           required>

                </div>


                <div class="mb-3">

                    <label for="description"
                           class="form-label text-gris-fonce">

                        Description

                    </label>

                    <textarea name="description"
                              id="description"
                              class="form-control">{$tva['description']}</textarea>

                </div>


                <div class="d-flex justify-content-center gap-2 mt-3">

                    <button type="submit"
                            class="btn text-white">

                        Modifier la TVA

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

HTML;


// ----------------- Inclusion du layout -----------------

require ROOT . 'layout.php';