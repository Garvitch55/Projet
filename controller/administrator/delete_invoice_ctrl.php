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

    $id_invoice = $_POST['id_invoice'] ?? null;

    if (!$id_invoice) {
        header("Location: ../../views/administrator/invoice.php?status=danger&message=Facture introuvable");
        exit;
    }

    try {
        $pdo = getPDO();

        // Suppression de la facture (les lignes de facture seront supprimées automatiquement grâce à ON DELETE CASCADE)
        $sql = "DELETE FROM invoices WHERE id_invoice = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_invoice]);

        header("Location: ../../views/administrator/invoice.php?status=success&message=La facture a bien été supprimée avec succès");
        exit;

    } catch (PDOException $e) {
        $error = $e->getMessage();
        header("Location: ../../views/administrator/invoice.php?status=danger&message=" . urlencode($error));
        exit;
    }

} else {
    // Accès direct interdit
    header("Location: ../../index.php");
    exit;
}