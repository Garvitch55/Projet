<?php

session_start();

// -------------------------
// Vérification admin
// -------------------------
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'administrateur') {

    header("Location: ../../index.php");
    exit;

}

// -------------------------
// Vérification POST
// -------------------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header("Location: ../../index.php");
    exit;

}

// -------------------------
// Chargement config
// -------------------------
require_once __DIR__ . '/../../config.php';

// -------------------------
// Récupération ID
// -------------------------
$id_tva = $_POST['id_tva'] ?? null;

if (!$id_tva) {

    header("Location: ../../views/administrator/settings/list_tva.php?status=danger&message=TVA introuvable.");
    exit;

}

try {

    $pdo = getPDO();

    $stmt = $pdo->prepare("
        DELETE FROM tva
        WHERE id_tva = :id
    ");

    $stmt->execute([
        'id' => $id_tva
    ]);

    header("Location: ../../views/administrator/settings/list_tva.php?status=success&message=La TVA a bien été supprimée avec succès.");

} catch (PDOException $e) {

    header("Location: ../../views/administrator/settings/list_tva.php?status=danger&message=Erreur lors de la suppression.");

}