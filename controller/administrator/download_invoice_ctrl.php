<?php
// download_invoice_ctrl.php (pour factures)
require_once '../../config.php';
require_once '../../vendor/autoload.php'; // Assure-toi que Dompdf est installé via Composer

use Dompdf\Dompdf;

session_start();

// -------------------------
// Vérification admin
// -------------------------
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'administrateur') {
    header("Location: ../../index.php");
    exit;
}

// -------------------------
// Récupérer l'ID de la facture
// -------------------------
$id_invoice = $_GET['id'] ?? null;
if (!$id_invoice) {
    die("Facture introuvable");
}

$pdo = getPDO();

// -------------------------
// Récupérer la facture et le client
// -------------------------
$stmt = $pdo->prepare("
    SELECT i.*, c.firstname, c.lastname 
    FROM invoices i 
    JOIN gestion_client c ON i.client_id = c.id_client 
    WHERE i.id_invoice = ?
");
$stmt->execute([$id_invoice]);
$invoice = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$invoice) {
    die("Facture introuvable");
}

// -------------------------
// Récupérer les lignes de la facture
// -------------------------
$itemsStmt = $pdo->prepare("SELECT * FROM invoice_items WHERE invoice_id = ?");
$itemsStmt->execute([$id_invoice]);
$items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

// -------------------------
// Générer le HTML de la facture
// -------------------------
ob_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Facture <?= htmlentities($invoice['invoice_number']) ?></title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h1 { color: #f08c00; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background: #eee; }
        .totals { margin-top: 20px; width: 300px; float: right; }
        .totals td { border: none; padding: 4px; text-align: right; }
        .totals td:first-child { text-align: left; }
    </style>
</head>
<body>
    <h1>Facture: <?= htmlentities($invoice['invoice_number']) ?></h1>
    <p><strong>Client:</strong> <?= htmlentities($invoice['firstname'] . ' ' . $invoice['lastname']) ?></p>
    <p><strong>Date facture:</strong> <?= htmlentities($invoice['invoice_date']) ?></p>
    <p><strong>Date échéance:</strong> <?= htmlentities($invoice['due_date']) ?></p>

    <table>
        <thead>
            <tr>
                <th>Ouvrage</th>
                <th>Quantité</th>
                <th>PU</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($items as $item): ?>
            <tr>
                <td><?= htmlentities($item['description']) ?></td>
                <td><?= $item['quantity'] ?></td>
                <td><?= number_format($item['unit_price'], 2, '.', '') ?> €</td>
                <td><?= number_format($item['total_price'], 2, '.', '') ?> €</td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <table class="totals">
        <tr><td>Total HT:</td><td><?= number_format($invoice['total_ht'], 2, '.', '') ?> €</td></tr>
        <tr><td>TVA:</td><td><?= number_format($invoice['total_vat'], 2, '.', '') ?> €</td></tr>
        <tr><td>Total TTC:</td><td><?= number_format($invoice['total_ttc'], 2, '.', '') ?> €</td></tr>
    </table>
</body>
</html>
<?php
$html = ob_get_clean();

// -------------------------
// Générer le PDF avec Dompdf
// -------------------------
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// -------------------------
// Envoyer le PDF au navigateur
// -------------------------
$filename = 'Facture_' . $invoice['invoice_number'] . '.pdf';
$dompdf->stream($filename, ['Attachment' => false]); // false = affichage inline
exit;
?>