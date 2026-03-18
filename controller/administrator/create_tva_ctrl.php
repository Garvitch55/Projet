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
// Récupération des données
// -------------------------
$name = trim($_POST['name'] ?? '');
$rate = trim($_POST['rate'] ?? '');
$description = trim($_POST['description'] ?? '');

// -------------------------
// Validation
// -------------------------
if (empty($name) || empty($rate)) {

    header("Location: ../../views/administrator/settings/create_tva.php?status=danger&message=Veuillez remplir tous les champs obligatoires.");
    exit;

}

try {

    $pdo = getPDO();

    $stmt = $pdo->prepare("
        INSERT INTO tva
        (
            name,
            rate,
            description
        )
        VALUES
        (
            :name,
            :rate,
            :description
        )
    ");

    $stmt->execute([
        'name' => $name,
        'rate' => $rate,
        'description' => $description
    ]);

    header("Location: ../../views/administrator/settings/list_tva.php?status=success&message=La TVA a bien été créée avec succès.");

} catch (PDOException $e) {

    header("Location: ../../views/administrator/settings/create_tva.php?status=danger&message=Erreur lors de la création de la TVA.");

}