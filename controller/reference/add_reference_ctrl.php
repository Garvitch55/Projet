<?php
// controller/reference/add_reference_ctrl.php

session_start();

// ----------------- Vérification de connexion et rôle -----------------
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'administrateur') {
    header("Location: ../../settings/add_reference.php?status=danger&message=" . urlencode("Accès refusé."));
    exit;
}

require_once __DIR__ . '/../../config.php';

// ----------------- Récupération des données du formulaire -----------------
$reference_name = $_POST['reference_name'] ?? '';
$reference_site = $_POST['reference_site'] ?? '';
$reference_description = $_POST['reference_description'] ?? '';
$completion_date = $_POST['completion_date'] ?? '';
$image_name = null;

// ----------------- Validation des champs obligatoires -----------------
if (!empty($reference_name) && !empty($reference_site) && !empty($completion_date)) {

    // ----------------- Gestion de l'upload d'image -----------------
    if (!empty($_FILES['reference_image']['name'])) {
        $upload_dir = __DIR__ . '/../../images/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        $tmp_name = $_FILES['reference_image']['tmp_name'];
        $original_name = basename($_FILES['reference_image']['name']);
        $ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
        $allowed_ext = ['jpg','jpeg','png','gif'];

        if (!in_array($ext, $allowed_ext)) {
            $error = "Extension non autorisée.";
        } else {
            $image_name = uniqid('ref_') . '.' . $ext;
            $destination = $upload_dir . $image_name;

            if (!move_uploaded_file($tmp_name, $destination)) {
                $error = "Erreur lors de l'upload de l'image.";
            }
        }
    }

    // ----------------- Insertion en base -----------------
    if (empty($error)) {
        $pdo = getPDO();

        $stmt = $pdo->prepare(
            "INSERT INTO reference_management (name, site, description, Completion_date, image) 
             VALUES (?, ?, ?, ?, ?)"
        );

        if ($stmt->execute([
            $reference_name,
            $reference_site,
            $reference_description,
            $completion_date,
            $image_name
        ])) {

            header("Location: ../../views/administrator/settings/add_reference.php?status=success&message=" . urlencode("Référence ajoutée avec succès !"));
            exit;

        } else {
            $error = "Erreur lors de l'ajout en base.";
        }
    }

} else {
    $error = "Le nom, le site et la date de réalisation sont obligatoires.";
}

// ----------------- Redirection avec message d'erreur -----------------
if (!empty($error)) {
    header("Location: ../../views/administrator/settings/add_reference.php?status=danger&message=" . urlencode($error));
    exit;
}