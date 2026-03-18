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
$client_id     = $_POST['client_id'] ?? null;
$quote_id      = $_POST['quote_id'] ?? null;
$invoice_number= $_POST['invoice_number'] ?? '';
$invoice_date  = $_POST['invoice_date'] ?? '';
$due_date      = $_POST['due_date'] ?? '';
$status        = $_POST['status'] ?? '';
$tva_id        = $_POST['tva_id'] ?? null;
$total_ht      = $_POST['total_ht'] ?? 0;
$total_vat     = $_POST['total_vat'] ?? 0;
$total_ttc     = $_POST['total_ttc'] ?? 0;

// Vérification des champs obligatoires
if (!$client_id || !$invoice_number || !$invoice_date || !$due_date || !$tva_id) {
    header("Location: ../../views/administrator/create_invoice.php?status=danger&message=Veuillez remplir tous les champs obligatoires");
    exit;
}

try {
    $pdo = getPDO();

    // -------------------------
    // Récupérer le taux de TVA sélectionné
    // -------------------------
    $stmtTva = $pdo->prepare("SELECT rate FROM tva WHERE id_tva = ?");
    $stmtTva->execute([$tva_id]);
    $tvaData = $stmtTva->fetch(PDO::FETCH_ASSOC);
    $tvaRate = $tvaData['rate'] ?? 0;

    // -------------------------
    // Insertion de la facture
    // -------------------------
    $stmt = $pdo->prepare("
        INSERT INTO invoices 
        (quote_id, client_id, invoice_number, invoice_date, due_date, status, total_ht, total_vat, total_ttc, created_at)
        VALUES (:quote_id, :client_id, :invoice_number, :invoice_date, :due_date, :status, :total_ht, :total_vat, :total_ttc, :created_at)
    ");

    $stmt->execute([
        'quote_id'       => $quote_id ?: null,
        'client_id'      => $client_id,
        'invoice_number' => $invoice_number,
        'invoice_date'   => $invoice_date,
        'due_date'       => $due_date,
        'status'         => $status,
        'total_ht'       => $total_ht,
        'total_vat'      => $total_vat,
        'total_ttc'      => $total_ttc,
        'created_at'     => date('Y-m-d H:i:s')
    ]);

    // -------------------------
    // Récupérer l'id de la facture créée
    // -------------------------
    $invoiceId = $pdo->lastInsertId();

    // -------------------------
    // Ajouter les lignes de facture
    // -------------------------
    $workIds    = $_POST['work_id'] ?? [];
    $quantities = $_POST['quantity'] ?? [];

    $stmtItem = $pdo->prepare("
        INSERT INTO invoice_items 
        (invoice_id, work_id, description, quantity, unit_price, total_price, created_at)
        VALUES (:invoice_id, :work_id, :description, :quantity, :unit_price, :total_price, :created_at)
    ");

    $totalHT = 0;

    foreach ($workIds as $index => $workId) {
        if (!$workId) continue;

        $quantity = $quantities[$index];
        $workStmt = $pdo->prepare("SELECT name, unit_price FROM works WHERE id_work = ?");
        $workStmt->execute([$workId]);
        $workData = $workStmt->fetch(PDO::FETCH_ASSOC);

        $lineTotal = $workData['unit_price'] * $quantity;
        $totalHT += $lineTotal;

        $stmtItem->execute([
            'invoice_id' => $invoiceId,
            'work_id'    => $workId,
            'description'=> $workData['name'],
            'quantity'   => $quantity,
            'unit_price' => $workData['unit_price'],
            'total_price'=> $lineTotal,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    // -------------------------
    // Calcul TVA et TTC réels
    // -------------------------
    $totalVAT = $totalHT * ($tvaRate / 100);
    $totalTTC = $totalHT + $totalVAT;

    // -------------------------
    // Mettre à jour la facture avec les totaux
    // -------------------------
    $updateInvoice = $pdo->prepare("
        UPDATE invoices SET total_ht = ?, total_vat = ?, total_ttc = ? WHERE id_invoice = ?
    ");
    $updateInvoice->execute([$totalHT, $totalVAT, $totalTTC, $invoiceId]);

    // -------------------------
    // Redirection finale
    // -------------------------
    header("Location: ../../views/administrator/invoice.php?status=success&message=Votre facture a été créée avec succès.");
    exit;

} catch (PDOException $e) {
    $error = $e->getMessage();
    header("Location: ../../views/administrator/create_invoice.php?status=danger&message=$error");
    exit;
}
?>