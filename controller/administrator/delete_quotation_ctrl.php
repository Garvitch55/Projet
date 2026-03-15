<?php
session_start();

// Empêche la suppression si l'utilisateur n'est pas admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'administrateur') {
    header("Location: ../../index.php");
    exit;
}

require_once __DIR__ . '/../../config.php';

// Vérifie que la requête est en POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_quote = $_POST['id_quote'] ?? null;

    if (!$id_quote) {
        header("Location: ../../views/administrator/quotation.php?status=danger&message=Devis introuvable");
        exit;
    }

    try {
        $pdo = getPDO();

        // Suppression du devis (les lignes de devis seront supprimées automatiquement grâce à ON DELETE CASCADE)
        $sql = "DELETE FROM quotes WHERE id_quote = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_quote]);

        header("Location: ../../views/administrator/quotation.php?status=success&message=Le devis a bien été supprimé avec succès");
        exit;

    } catch (PDOException $e) {
        $error = $e->getMessage();
        header("Location: ../../views/administrator/quotation.php?status=danger&message=" . urlencode($error));
        exit;
    }

} else {
    // Accès direct interdit
    header("Location: ../../index.php");
    exit;
}