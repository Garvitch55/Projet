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

$id_quote = $_POST['id_quote'] ?? null;
$quote_number = $_POST['quote_number'] ?? '';
$quote_date = $_POST['quote_date'] ?? '';
$status = $_POST['status'] ?? '';
$total_ht = $_POST['total_ht'] ?? 0;
$total_vat = $_POST['total_vat'] ?? 0;
$total_ttc = $_POST['total_ttc'] ?? 0;

if (!$id_quote) {
    header("Location: ../../views/administrator/quotation.php?status=danger&message=Devis introuvable");
    exit;
}

try {
    $pdo = getPDO();

    $stmt = $pdo->prepare("
        UPDATE quotes
        SET quote_number = :quote_number,
            quote_date = :quote_date,
            status = :status,
            total_ht = :total_ht,
            total_vat = :total_vat,
            total_ttc = :total_ttc
        WHERE id_quote = :id
    ");

    $stmt->execute([
        'quote_number' => $quote_number,
        'quote_date' => $quote_date,
        'status' => $status,
        'total_ht' => $total_ht,
        'total_vat' => $total_vat,
        'total_ttc' => $total_ttc,
        'id' => $id_quote
    ]);

    header("Location: ../../views/administrator/quotation.php?status=success&message=Devis mis à jour avec succès");
    exit;

} catch (PDOException $e) {
    $error = $e->getMessage();
    header("Location: ../../views/administrator/quotation.php?status=danger&message=$error");
    exit;
}