<?php
session_start();

// -------------------------
// Vérification admin
// -------------------------
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'administrateur') {
    header("Location: ../../index.php");
    exit;
}

require_once __DIR__ . '/../../config.php';

// -------------------------
// Vérification POST
// -------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_personnel = $_POST['id_personnel'] ?? null;

    if (!$id_personnel) {
        header("Location: ../../views/administrator/staff.php?status=danger&message=Membre du personnel introuvable");
        exit;
    }

    try {
        $pdo = getPDO();

        // -------------------------
        // Suppression du membre du personnel
        // -------------------------
        $stmt = $pdo->prepare("DELETE FROM gestion_personnel WHERE id_personnel = ?");
        $stmt->execute([$id_personnel]);

        header("Location: ../../views/administrator/settings/list_staff.php?status=success&message=Le membre du personnel a bien été supprimé avec succès");
        exit;

    } catch (PDOException $e) {
        $error = $e->getMessage();
        header("Location: ../../views/administrator/settings/list_staff.php?status=danger&message=" . urlencode($error));
        exit;
    }

} else {
    // Accès direct interdit
    header("Location: ../../index.php");
    exit;
}