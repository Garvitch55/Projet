<?php
// download_quotation_ctrl.php
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
// Récupérer l'ID du devis
// -------------------------
$id_quote = $_GET['id'] ?? null;
if (!$id_quote) {
    die("Devis introuvable");
}

$pdo = getPDO();

// -------------------------
// Récupérer le devis et client
// -------------------------
$stmt = $pdo->prepare("
    SELECT q.*, c.firstname, c.lastname 
    FROM quotes q 
    JOIN gestion_client c ON q.client_id = c.id_client 
    WHERE q.id_quote = ?
");
$stmt->execute([$id_quote]);
$quote = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$quote) {
    die("Devis introuvable");
}

// -------------------------
// Récupérer les lignes du devis
// -------------------------
$itemsStmt = $pdo->prepare("SELECT * FROM quote_items WHERE quote_id = ?");
$itemsStmt->execute([$id_quote]);
$items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

// -------------------------
// Générer le HTML du devis
// -------------------------
ob_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Devis <?= htmlentities($quote['quote_number']) ?></title>
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
    <h1>Devis: <?= htmlentities($quote['quote_number']) ?></h1>
    <p><strong>Client:</strong> <?= htmlentities($quote['firstname'] . ' ' . $quote['lastname']) ?></p>
    <p><strong>Date:</strong> <?= htmlentities($quote['quote_date']) ?></p>

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
        <tr><td>Total HT:</td><td><?= number_format($quote['total_ht'], 2, '.', '') ?> €</td></tr>
        <tr><td>TVA:</td><td><?= number_format($quote['total_vat'], 2, '.', '') ?> €</td></tr>
        <tr><td>Total TTC:</td><td><?= number_format($quote['total_ttc'], 2, '.', '') ?> €</td></tr>
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
$filename = 'Devis_' . $quote['quote_number'] . '.pdf';
$dompdf->stream($filename, ['Attachment' => false]); // false = affichage inline
exit;