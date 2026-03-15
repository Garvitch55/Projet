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

$client_id = $_POST['client_id'] ?? null;
$quote_number = $_POST['quote_number'] ?? '';
$quote_date = $_POST['quote_date'] ?? '';
$status = $_POST['status'] ?? '';
$total_ht = $_POST['total_ht'] ?? 0;
$total_vat = $_POST['total_vat'] ?? 0;
$total_ttc = $_POST['total_ttc'] ?? 0;

if (!$client_id || !$quote_number || !$quote_date) {
    header("Location: ../../views/administrator/settings/create_quotation.php?status=danger&message=Veuillez remplir tous les champs obligatoires");
    exit;
}

try {
    $pdo = getPDO();

    $stmt = $pdo->prepare("
        INSERT INTO quotes (client_id, quote_number, quote_date, status, total_ht, total_vat, total_ttc, created_at)
        VALUES (:client_id, :quote_number, :quote_date, :status, :total_ht, :total_vat, :total_ttc, :created_at)
    ");

    $stmt->execute([
        'client_id' => $client_id,
        'quote_number' => $quote_number,
        'quote_date' => $quote_date,
        'status' => $status,
        'total_ht' => $total_ht,
        'total_vat' => $total_vat,
        'total_ttc' => $total_ttc,
        'created_at' => date('Y-m-d H:i:s')
    ]);

    header("Location: ../../views/administrator/quotation.php?status=success&message=Devis créé avec succès");
    exit;

} catch (PDOException $e) {
    $error = $e->getMessage();
    header("Location: ../../views/administrator/create_quotation.php?status=danger&message=$error");
    exit;
}