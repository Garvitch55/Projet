<?php
// download_invoice.php
require_once '../../config.php';
require_once '../../head.php';
require_once '../../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Démarrage session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérification administrateur
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'client') {
    header("Location: ../../index.php");
    exit;
}

// Récupération ID de la facture
$id_invoice = $_GET['id'] ?? null;
if (!$id_invoice) die("Facture introuvable");

// Connexion PDO
$pdo = getPDO();
$pdo->exec("SET NAMES 'utf8mb4'");

// Récupération facture + client (avec adresse)
$stmt = $pdo->prepare("
    SELECT i.*, c.firstname, c.lastname, c.rue, c.cp, c.ville
    FROM invoices i
    JOIN gestion_client c ON i.client_id = c.id_client
    WHERE i.id_invoice = ?
");
$stmt->execute([$id_invoice]);
$invoice = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$invoice) die("Facture introuvable");

// Récupération lignes de facture
$itemsStmt = $pdo->prepare("SELECT * FROM invoice_items WHERE invoice_id = ?");
$itemsStmt->execute([$id_invoice]);
$items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC) ?? [];

// Sécurisation variables
$invoice_number   = htmlentities($invoice['invoice_number'] ?? 'N/A', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$client_name      = htmlentities(($invoice['firstname'] ?? '') . ' ' . ($invoice['lastname'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$invoice_date     = htmlentities($invoice['invoice_date'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$due_date         = htmlentities($invoice['due_date'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$client_rue       = htmlentities($invoice['rue'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$client_cpville   = htmlentities(($invoice['cp'] ?? '') . ' ' . ($invoice['ville'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

// Sécurisation des items
foreach ($items as &$item) {
    $item['description'] = htmlentities((string)($item['description'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $item['quantity']    = intval($item['quantity'] ?? 0);
    $item['unit_price']  = floatval($item['unit_price'] ?? 0);
    $item['total_price'] = floatval($item['total_price'] ?? 0);
}
unset($item);

// Préparer logo en Base64
$logoPath = __DIR__ . '/../../assets/statics/images/logo.png';
$logoData = '';
if (file_exists($logoPath)) {
    $type = pathinfo($logoPath, PATHINFO_EXTENSION);
    $data = file_get_contents($logoPath);
    $logoData = 'data:image/' . $type . ';base64,' . base64_encode($data);
}

// Générer HTML
ob_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Facture <?= $invoice_number ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { font-size: 12px; }
.table th, .table td { padding: 8px; border: 1px solid #000 !important; }
.table thead th { background-color: rgba(227, 143, 60, 0.7); }
.totals { width: 300px; margin-left: auto; }
.totals td { border: none; padding: 4px; text-align: right; }
</style>
</head>
<body>

<div class="d-flex align-items-start mb-3">
    <div class="company">
        <?php if($logoData): ?>
            <img src="<?= $logoData ?>" alt="Logo Entreprise" style="max-height:80px;">
        <?php endif; ?>
        <h4 class="mb-1"><b>A.GARNIER CONSTRUCTION</b></h4>
        <p class="mb-0" style="font-size:11px;">15 allée du pré l'évêque</p>
        <p class="mb-0" style="font-size:11px;">55100 Verdun</p>
        <p class="mb-0" style="font-size:11px;"><b>Tel: </b>03 29 45 67 89</p>
        <p class="mb-0" style="font-size:11px;"><b>Email: </b>contact@agarnierconstruction.com</p>
    </div>

    <div class="client ms-auto text-start" style="max-width:250px;">
        <p class="mb-0"><b>Client:</b></p>
        <p class="mb-0"><?= $client_name ?></p>
        <p class="mb-0"><?= $client_rue ?><br><?= $client_cpville ?></p>
        <p class="mb-0"><b>Date facture:</b> <?= $invoice_date ?></p>
        <p class="mb-0"><b>Date échéance:</b> <?= $due_date ?></p>
    </div>
</div>

<h2 class="text-center mb-3">Facture: <?= $invoice_number ?></h2>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Ouvrage</th>
            <th>Quantité</th>
            <th>PU</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($items as $item): ?>
        <tr>
            <td><?= $item['description'] ?></td>
            <td><?= $item['quantity'] ?></td>
            <td><?= number_format($item['unit_price'], 2, '.', '') ?> €</td>
            <td><?= number_format($item['total_price'], 2, '.', '') ?> €</td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<table class="totals mt-3">
    <tr>
        <td><b>Total HT:</b></td>
        <td><?= number_format(floatval($invoice['total_ht'] ?? 0), 2, '.', '') ?> €</td>
    </tr>
    <tr>
        <td><b>TVA:</b></td>
        <td><?= number_format(floatval($invoice['total_vat'] ?? 0), 2, '.', '') ?> €</td>
    </tr>
    <tr>
        <td><b>Total TTC:</b></td>
        <td><?= number_format(floatval($invoice['total_ttc'] ?? 0), 2, '.', '') ?> €</td>
    </tr>
</table>

</body>
</html>
<?php
$html = ob_get_clean();

// Options Dompdf
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');

try {
    $dompdf->render();
} catch (\Exception $e) {
    echo "<h2>Erreur Dompdf :</h2><pre>" . $e->getMessage() . "</pre>"; 
    exit;
}

// Envoi PDF au navigateur
$filename = 'Facture_' . $invoice_number . '.pdf';
$dompdf->stream($filename, ['Attachment' => false]);
exit;
?>