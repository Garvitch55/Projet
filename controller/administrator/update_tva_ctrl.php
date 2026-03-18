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


require_once __DIR__ . '/../../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header("Location: ../../views/administrator/settings/list_tva.php");
    exit;

}

$id = $_POST['id_tva'] ?? null;
$name = trim($_POST['name'] ?? '');
$rate = trim($_POST['rate'] ?? '');
$description = trim($_POST['description'] ?? '');

if (!$id || empty($name) || empty($rate)) {

    header("Location: ../../views/administrator/settings/list_tva.php?status=danger&message=Champs obligatoires");
    exit;

}

try {

    $pdo = getPDO();

    $stmt = $pdo->prepare("
        UPDATE tva
        SET
            name = :name,
            rate = :rate,
            description = :description
        WHERE id_tva = :id
    ");

    $stmt->execute([
        'name' => $name,
        'rate' => $rate,
        'description' => $description,
        'id' => $id
    ]);

    header("Location: ../../views/administrator/settings/list_tva.php?status=success&message=La TVA a bien été mise à jour avec succès.");

} catch (PDOException $e) {

    header("Location: ../../views/administrator/settings/list_tva.php?status=danger&message=Erreur modification");

}