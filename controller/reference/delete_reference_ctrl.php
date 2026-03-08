<?php

session_start();
require_once __DIR__ . '/../../config.php';

// Vérification connexion
if (!isset($_SESSION['id'])) {
    header("Location: ../../../login.php");
    exit;
}

// Vérification rôle
if ($_SESSION['role'] !== 'administrateur') {
    header("Location: ../../../index.php");
    exit;
}

// Vérifier ID
if (!isset($_GET['id'])) {
    header("Location: ../../views/administrator/settings/list_reference.php?status=danger&message=" . urlencode("Référence introuvable."));
    exit;
}

$reference_id = $_GET['id'];

$pdo = getPDO();

// récupérer l'image avant suppression
$stmt = $pdo->prepare("SELECT image FROM reference_management WHERE id = ?");
$stmt->execute([$reference_id]);
$reference = $stmt->fetch();

if (!$reference) {
    header("Location: ../../views/administrator/settings/list_reference.php?status=danger&message=" . urlencode("Référence introuvable."));
    exit;
}

// supprimer l'image du serveur
if (!empty($reference['image'])) {

    $image_path = __DIR__ . '/../../../images/' . $reference['image'];

    if (file_exists($image_path)) {
        unlink($image_path);
    }
}

// supprimer la référence
$stmt = $pdo->prepare("DELETE FROM reference_management WHERE id = ?");
$stmt->execute([$reference_id]);

header("Location: ../../views/administrator/settings/list_reference.php?status=success&message=" . urlencode("Référence supprimée."));
exit;