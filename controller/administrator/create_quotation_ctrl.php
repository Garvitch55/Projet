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

// -------------------------
// Récupération des champs du formulaire
// -------------------------
$client_id = $_POST['client_id'] ?? null;
$quote_number = $_POST['quote_number'] ?? '';
$quote_date = $_POST['quote_date'] ?? '';
$status = $_POST['status'] ?? '';
$total_ht = $_POST['total_ht'] ?? 0;
$total_vat = $_POST['total_vat'] ?? 0;
$total_ttc = $_POST['total_ttc'] ?? 0;

// Vérification des champs obligatoires
if (!$client_id || !$quote_number || !$quote_date) {
    header("Location: ../../views/administrator/settings/create_quotation.php?status=danger&message=Veuillez remplir tous les champs obligatoires");
    exit;
}

try {
    $pdo = getPDO();

    // -------------------------
    // Insertion du devis
    // -------------------------
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

    // -------------------------
    // Récupérer le dernier id_quote
    // -------------------------
    $quoteId = $pdo->lastInsertId();

    // -------------------------
    // Ajouter les lignes de devis
    // -------------------------
    $workIds = $_POST['work_id'] ?? [];
    $quantities = $_POST['quantity'] ?? [];

    $stmtItem = $pdo->prepare("
        INSERT INTO quote_items (quote_id, work_id, description, quantity, unit_price, total_price, created_at)
        VALUES (:quote_id, :work_id, :description, :quantity, :unit_price, :total_price, :created_at)
    ");

    $totalHT = 0;

    foreach ($workIds as $index => $workId) {
        if (!$workId) continue;

        $quantity = $quantities[$index];
        $work = $pdo->prepare("SELECT name, unit_price FROM works WHERE id_work = ?");
        $work->execute([$workId]);
        $workData = $work->fetch(PDO::FETCH_ASSOC);

        $totalPrice = $workData['unit_price'] * $quantity;
        $totalHT += $totalPrice;

        $stmtItem->execute([
            'quote_id' => $quoteId,
            'work_id' => $workId,
            'description' => $workData['name'],
            'quantity' => $quantity,
            'unit_price' => $workData['unit_price'],
            'total_price' => $totalPrice,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    // -------------------------
    // Calcul TVA et TTC
    // -------------------------
    $totalVAT = $totalHT * 0.20;
    $totalTTC = $totalHT + $totalVAT;

    // -------------------------
    // Mettre à jour le devis avec totaux réels
    // -------------------------
    $updateQuote = $pdo->prepare("
        UPDATE quotes SET total_ht = ?, total_vat = ?, total_ttc = ? WHERE id_quote = ?
    ");
    $updateQuote->execute([$totalHT, $totalVAT, $totalTTC, $quoteId]);

    // -------------------------
    // Redirection finale
    // -------------------------
    header("Location: ../../views/administrator/quotation.php?status=success&message=Devis créé avec succès");
    exit;

} catch (PDOException $e) {
    $error = $e->getMessage();
    header("Location: ../../views/administrator/create_quotation.php?status=danger&message=$error");
    exit;
}