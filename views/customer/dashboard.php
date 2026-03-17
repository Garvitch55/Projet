<?php

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../head.php';

// -------------------------
// Sécurité
// -------------------------
if (!isset($_SESSION['id'])) {
    header("Location: ../login.php?status=danger&message=Veuillez vous connecter.");
    exit;
}

if ($_SESSION['role'] !== 'client') {
    header("Location: ../../index.php?status=danger&message=Accès refusé.");
    exit;
}

$title = "Tableau de bord client";

$pdo = getPDO();
$client_id = $_SESSION['id'];

// -------------------------
// STATS
// -------------------------

// Nombre de devis
$stmt = $pdo->prepare("SELECT COUNT(*) FROM quotes WHERE client_id = ?");
$stmt->execute([$client_id]);
$totalQuotes = $stmt->fetchColumn();

// Nombre de factures
$stmt = $pdo->prepare("SELECT COUNT(*) FROM invoices WHERE client_id = ?");
$stmt->execute([$client_id]);
$totalInvoices = $stmt->fetchColumn();

// Total factures TTC
$stmt = $pdo->prepare("SELECT SUM(total_ttc) FROM invoices WHERE client_id = ?");
$stmt->execute([$client_id]);
$totalAmount = $stmt->fetchColumn() ?? 0;

// -------------------------
// DERNIERS DEVIS
// -------------------------
$stmt = $pdo->prepare("
    SELECT * FROM quotes 
    WHERE client_id = ? 
    ORDER BY created_at DESC 
    LIMIT 3
");
$stmt->execute([$client_id]);
$quotes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// -------------------------
// DERNIÈRES FACTURES
// -------------------------
$stmt = $pdo->prepare("
    SELECT * FROM invoices 
    WHERE client_id = ? 
    ORDER BY created_at DESC 
    LIMIT 3
");
$stmt->execute([$client_id]);
$invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);

// -------------------------
// FACTURES EN ATTENTE DE PAIEMENT
// -------------------------
$stmt = $pdo->prepare("
    SELECT * FROM invoices 
    WHERE client_id = ? AND status = 'en attente de paiement'
    ORDER BY due_date ASC
");
$stmt->execute([$client_id]);
$pendingInvoices = $stmt->fetchAll(PDO::FETCH_ASSOC);

// -------------------------
ob_start();
?>

<?php
ob_start();
require ROOT . "notification.php";
$notification = ob_get_clean();
?>

<section class="m-4">

    <!-- TITRE -->
    <div class="mb-4">
        <h1 class="text-orange-fonce">Tableau de bord</h1>
        <p>Bienvenue dans votre espace client</p>
    </div>

    <!-- ================= STATS ================= -->
    <div class="row mb-4">

        <div class="col-md-3">
            <div class="card text-center shadow-sm p-3 bg-gris-fonce text-white">
                <h5>Devis</h5>
                <h2><?= $totalQuotes ?></h2>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-center shadow-sm p-3 bg-gris-fonce text-white">
                <h5>Factures</h5>
                <h2><?= $totalInvoices ?></h2>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-center shadow-sm p-3 bg-gris-fonce text-white">
                <h5>Total payé</h5>
                <h2><?= number_format($totalAmount, 2, ',', ' ') ?> €</h2>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-center shadow-sm p-3 bg-gris-fonce text-white">
                <h5>Factures en attente</h5>
                <h2><?= count($pendingInvoices) ?></h2>
            </div>
        </div>

    </div>

    <div class="row">

        <!-- ================= DEVIS ================= -->
        <div class="col-md-6">
            <div class="card shadow-sm p-4 mb-4 gap-3">
                <h5 class="text-orange-fonce">Mes derniers devis</h5>

                <?php if (!empty($quotes)): ?>
                    <ul class="list-group">
                        <?php foreach ($quotes as $q): 
    $status_class = match($q['status']) {
        'en attente' => 'bg-info',
        'signé'      => 'bg-success',
        'annulé'     => 'bg-warning',
        default      => 'bg-secondary'
    };
?>
    <li class="list-group-item d-flex justify-content-between align-items-center p-3">
        <div>
            <strong><?= htmlentities($q['quote_number']) ?></strong><br>
            <small><?= htmlentities($q['quote_date']) ?></small>
        </div>

        <span class="badge <?= $status_class ?>"><?= ucfirst($q['status']) ?></span>
    </li>
<?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>Aucun devis</p>
                <?php endif; ?>

                <a href="/projet/views/customer/quotation.php" class="btn text-white">
                    Voir tous les devis
                </a>
            </div>
        </div>

        <!-- ================= FACTURES ================= -->
        <div class="col-md-6">
            <div class="card shadow-sm p-4 mb-4 gap-3">
                <h5 class="text-orange-fonce">Mes dernières factures</h5>

                <?php if (!empty($invoices)): ?>
                    <ul class="list-group">
                        <?php foreach ($invoices as $inv): 
    
    
    
    $status_class = match($inv['status']) {
        'brouillon' => 'bg-secondary',
        'en attente de paiement' => 'bg-danger',
        'payée'     => 'bg-success',
        'annulée'   => 'bg-warning',
        default     => 'bg-secondary'
    };
?>
    <li class="list-group-item d-flex justify-content-between align-items-center p-3">
        <div>
            <strong><?= htmlentities($inv['invoice_number']) ?></strong><br>
            <small><?= htmlentities($inv['invoice_date']) ?></small>
        </div>

        <span class="badge <?= $status_class ?>"><?= ucfirst($inv['status']) ?></span>
    </li>
<?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>Aucune facture</p>
                <?php endif; ?>

                <a href="/projet/views/customer/invoice.php" class="btn text-white">
                    Voir toutes les factures
                </a>
            </div>
        </div>

    </div>

    <!-- ================= FACTURES EN ATTENTE ================= -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm p-4 mb-4 gap-3">
                <h5 class="text-orange-fonce">Factures en attente de paiement</h5>

                <?php if (!empty($pendingInvoices)): ?>
                    <ul class="list-group">
                        <?php foreach ($pendingInvoices as $p): 
    $status_class = 'bg-danger';
?>
    <li class="list-group-item d-flex justify-content-between align-items-center p-3">
        <div>
            <strong><?= htmlentities($p['invoice_number']) ?></strong><br>
            <small>Échéance : <?= htmlentities($p['due_date']) ?></small>
        </div>

        <span class="badge <?= $status_class ?>"><?= ucfirst($p['status']) ?></span>
    </li>
<?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>Aucune facture en attente</p>
                <?php endif; ?>

                <a href="/projet/views/customer/invoice.php" class="btn text-white">
                    Voir toutes les factures
                </a>
            </div>
        </div>
    </div>

</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layout.php';
?>



