<?php
// controller/reference/update_reference_ctrl.php

session_start();

// ----------------- Vérification de connexion -----------------
if (!isset($_SESSION['id'])) {
    header("Location: ../../login.php?status=danger&message=" . urlencode("Veuillez vous connecter."));
    exit;
}

// ----------------- Vérification du rôle -----------------
if ($_SESSION['role'] !== 'administrateur') {
    header("Location: ../../../index.php?status=danger&message=" . urlencode("Accès refusé."));
    exit;
}

// ----------------- Inclure config -----------------
require_once __DIR__ . '/../../config.php'; // chemin corrigé selon ton projet

// ----------------- Interdire accès direct -----------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../settings/list_references.php?status=danger&message=" . urlencode("Accès direct interdit."));
    exit;
}

// ----------------- Vérifier que l'ID est fourni -----------------
$reference_id = $_POST['reference_id'] ?? null;
if (!$reference_id) {
    header("Location: ../../settings/list_references.php?status=danger&message=" . urlencode("Référence introuvable."));
    exit;
}

// ----------------- Récupérer la référence existante -----------------
$pdo = getPDO();
$stmt = $pdo->prepare("SELECT * FROM reference_management WHERE id = ?");
$stmt->execute([$reference_id]);
$reference = $stmt->fetch();

if (!$reference) {
    header("Location: ../../settings/list_references.php?status=danger&message=" . urlencode("Référence introuvable."));
    exit;
}

// ----------------- Récupérer les données du formulaire -----------------
$reference_name = $_POST['reference_name'] ?? '';
$reference_description = $_POST['reference_description'] ?? '';
$completion_date = $_POST['completion_date'] ?? '';
$site = $_POST['site'] ?? '';
$image_name = $reference['image']; // garder l'image existante par défaut
$error = '';

// ----------------- Gestion de l'upload d'image -----------------
if (!empty($_FILES['reference_image']['name'])) {
    $upload_dir = __DIR__ . '/../../images/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

    $tmp_name = $_FILES['reference_image']['tmp_name'];
    $original_name = basename($_FILES['reference_image']['name']);
    $ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
    $allowed_ext = ['jpg','jpeg','png','gif'];

    if (!in_array($ext, $allowed_ext)) {
        $error = "Extension non autorisée. Seuls jpg, png, gif sont acceptés.";
    } else {
        $image_name = uniqid('ref_') . '.' . $ext;
        $destination = $upload_dir . $image_name;

        if (!move_uploaded_file($tmp_name, $destination)) {
            $error = "Erreur lors de l'upload de l'image.";
        } else {
            // Supprimer l'ancienne image si elle existe
            if ($reference['image'] && file_exists($upload_dir . $reference['image'])) {
                unlink($upload_dir . $reference['image']);
            }
        }
    }
}

// ----------------- Mise à jour en base -----------------
if (empty($error)) {
    $stmt = $pdo->prepare(
        "UPDATE reference_management SET name = ?, description = ?, Completion_date = ?, site = ?, image = ? WHERE id = ?"
    );
    if ($stmt->execute([$reference_name, $reference_description, $completion_date, $site, $image_name, $reference_id])) {
        header("Location: ../../views/administrator/settings/update_reference.php?id=$reference_id&status=success&message=" . urlencode("Référence modifiée avec succès !"));
        exit;
    } else {
        $error = "Erreur lors de la mise à jour.";
        header("Location: ../../views/administrator/settings/update_reference.php?id=$reference_id&status=danger&message=" . urlencode($error));
        exit;
    }
} else {
    header("Location: ../../views/administrator/settings/update_reference.php?id=$reference_id&status=danger&message=" . urlencode($error));
    exit;
}