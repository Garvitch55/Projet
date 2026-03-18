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
$id_invoice = $_POST['id_invoice'] ?? null;
$invoice_number = $_POST['invoice_number'] ?? '';
$invoice_date = $_POST['invoice_date'] ?? '';
$due_date = $_POST['due_date'] ?? '';
$status = $_POST['status'] ?? '';
$client_id = $_POST['client_id'] ?? '';
$tva_rate = $_POST['tva_rate'] ?? 0;

$workIds = $_POST['work_id'] ?? [];
$quantities = $_POST['quantity'] ?? [];

$totalHT = $_POST['total_ht'] ?? 0;
$totalVAT = $_POST['total_vat'] ?? 0;
$totalTTC = $_POST['total_ttc'] ?? 0;

if (!$id_invoice || !$invoice_number || !$invoice_date || !$client_id) {
    header("Location: ../../views/administrator/create_invoice.php?status=danger&message=Veuillez remplir tous les champs obligatoires");
    exit;
}

try {
    $pdo = getPDO();
    $pdo->beginTransaction();

    // -------------------------
    // Mettre à jour la facture
    // -------------------------
   $stmt = $pdo->prepare("
    UPDATE invoices
    SET invoice_number = :invoice_number,
        invoice_date = :invoice_date,
        due_date = :due_date,
        status = :status,
        client_id = :client_id,
        total_ht = :total_ht,
        total_vat = :total_vat,
        total_ttc = :total_ttc
    WHERE id_invoice = :id_invoice
");

$stmt->execute([
    'invoice_number' => $invoice_number,
    'invoice_date' => $invoice_date,
    'due_date' => $due_date,
    'status' => $status,
    'client_id' => $client_id,
    'total_ht' => $total_ht,
    'total_vat' => $total_vat,
    'total_ttc' => $total_ttc,
    'id_invoice' => $id_invoice
]);

    // -------------------------
    // Supprimer les anciennes lignes
    // -------------------------
    $stmtDelete = $pdo->prepare("DELETE FROM invoice_items WHERE invoice_id = ?");
    $stmtDelete->execute([$id_invoice]);

    // -------------------------
    // Ajouter les lignes de facture
    // -------------------------
    $stmtItem = $pdo->prepare("
        INSERT INTO invoice_items
        (invoice_id, work_id, quantity, unit_price, total_price, created_at)
        VALUES (:invoice_id, :work_id, :quantity, :unit_price, :total_price, :created_at)
    ");

    $calculatedHT = 0;

    foreach ($workIds as $index => $workId) {
        if (!$workId) continue;

        $quantity = $quantities[$index] ?? 1;

        // Récupérer le prix unitaire du travail
        $stmtWork = $pdo->prepare("SELECT unit_price FROM works WHERE id_work = ?");
        $stmtWork->execute([$workId]);
        $work = $stmtWork->fetch(PDO::FETCH_ASSOC);
        if (!$work) continue;

        $unitPrice = $work['unit_price'];
        $lineTotal = $unitPrice * $quantity;
        $calculatedHT += $lineTotal;

        $stmtItem->execute([
            'invoice_id' => $id_invoice,
            'work_id' => $workId,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_price' => $lineTotal,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    // -------------------------
    // Calcul TVA et TTC réel
    // -------------------------
    $calculatedVAT = $calculatedHT * ($tva_rate/100);
    $calculatedTTC = $calculatedHT + $calculatedVAT;

    $stmtUpdateTotals = $pdo->prepare("
        UPDATE invoices SET total_ht = ?, total_vat = ?, total_ttc = ? WHERE id_invoice = ?
    ");
    $stmtUpdateTotals->execute([$calculatedHT, $calculatedVAT, $calculatedTTC, $id_invoice]);

    $pdo->commit();

    header("Location: ../../views/administrator/invoice.php?status=success&message=Votre facture a bien été mise à jour avec succès.");
    exit;

} catch (PDOException $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    $error = $e->getMessage();
    header("Location: ../../views/administrator/create_invoice.php?status=danger&message=$error");
    exit;
}
?>