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

if ($_SESSION['role'] !== 'administrateur') {
    header("Location: ../../index.php?status=danger&message=Accès refusé.");
    exit;
}

$title = "Dashboard administrateur";

$pdo = getPDO();

// -------------------------
// STATS
// -------------------------

$totalQuotes = $pdo->query("SELECT COUNT(*) FROM quotes")->fetchColumn();
$totalInvoices = $pdo->query("SELECT COUNT(*) FROM invoices")->fetchColumn();

// Factures en attente (nombre)
$stmt = $pdo->prepare("SELECT COUNT(*) FROM invoices WHERE status = ?");
$stmt->execute(['en attente de paiement']);
$totalPending = $stmt->fetchColumn();

// Montant factures payées
$stmt = $pdo->prepare("SELECT SUM(total_ttc) FROM invoices WHERE status = ?");
$stmt->execute(['payée']);
$totalPaid = $stmt->fetchColumn() ?? 0;

// Montant factures en attente
$stmt = $pdo->prepare("SELECT SUM(total_ttc) FROM invoices WHERE status = ?");
$stmt->execute(['en attente de paiement']);
$totalPendingAmount = $stmt->fetchColumn() ?? 0;

// Montant devis en attente
$stmt = $pdo->prepare("SELECT SUM(total_ttc) FROM quotes WHERE status = ?");
$stmt->execute(['en attente']);
$totalPendingQuotesAmount = $stmt->fetchColumn() ?? 0;

// Messages non lus
$stmt = $pdo->prepare("SELECT COUNT(*) FROM contact WHERE is_read = ?");
$stmt->execute([0]);
$totalMessages = $stmt->fetchColumn();

// -------------------------
// FACTURES EN ATTENTE (5 dernières)
// -------------------------
$stmt = $pdo->prepare("
    SELECT i.id_invoice, i.invoice_number, i.invoice_date, i.due_date, i.status,
           c.firstname, c.lastname
    FROM invoices i
    JOIN gestion_client c ON i.client_id = c.id_client
    WHERE i.status = ?
    ORDER BY i.due_date ASC
");
$stmt->execute(['en attente de paiement']);
$pendingInvoices = $stmt->fetchAll(PDO::FETCH_ASSOC);

// -------------------------
// MESSAGES (5 derniers non lus)
// -------------------------
$stmt = $pdo->prepare("
    SELECT first_name, last_name, subject, created_at
    FROM contact
    WHERE is_read = ?
    ORDER BY created_at DESC
");
$stmt->execute([0]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// -------------------------
// 3 DERNIERS DEVIS
// -------------------------
$quotes = $pdo->query("
    SELECT q.id_quote, q.quote_number, q.quote_date, q.status,
           c.firstname, c.lastname
    FROM quotes q
    JOIN gestion_client c ON q.client_id = c.id_client
    ORDER BY q.created_at DESC
    LIMIT 3
")->fetchAll(PDO::FETCH_ASSOC);

// -------------------------
// 3 DERNIÈRES FACTURES
// -------------------------
$invoices = $pdo->query("
    SELECT i.id_invoice, i.invoice_number, i.invoice_date, i.status,
           c.firstname, c.lastname
    FROM invoices i
    JOIN gestion_client c ON i.client_id = c.id_client
    ORDER BY i.created_at DESC
    LIMIT 3
")->fetchAll(PDO::FETCH_ASSOC);

// -------------------------
ob_start();
?>

<section class="m-4">

    <!-- TITRE -->
    <div class="mb-4">
        <h1 class="text-orange-fonce">Dashboard administrateur</h1>
        <p>Bienvenue dans votre espace de gestion</p>
    </div>

    <!-- ================= STATS ================= -->
    <div class="row mb-4">

        <div class="col-md-2">
            <div class="card text-center shadow-sm p-3 bg-gris-fonce text-white">
                <h6 class="text-white">Devis</h6>
                <h3><?= $totalQuotes ?></h3>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card text-center shadow-sm p-3 bg-gris-fonce text-white">
                <h6 class="text-white">Factures</h6>
                <h3><?= $totalInvoices ?></h3>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card text-center shadow-sm p-3 bg-danger text-white">
                <h6 class="text-white">Factures en attente</h6>
                <h3><?= $totalPending ?></h3>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card text-center shadow-sm p-3 bg-info text-white">
                <h6 class="text-white">Montant devis en attente</h6>
                <h3><?= number_format($totalPendingQuotesAmount, 2, ',', ' ') ?> €</h3>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card text-center shadow-sm p-3 bg-orange-fonce text-white">
                <h6 class="text-white">Facturé</h6>
                <h3><?= number_format($totalPaid, 2, ',', ' ') ?> €</h3>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card text-center shadow-sm p-3 bg-danger text-white">
                <h6 class="text-white">À encaisser</h6>
                <h3><?= number_format($totalPendingAmount, 2, ',', ' ') ?> €</h3>
            </div>
        </div>

    </div>

    <div class="row">

       <!-- FACTURES EN ATTENTE -->
<div class="col-md-6">
    <div class="card shadow-sm p-4 mb-4">
        <h5 class="text-orange-fonce">Factures en attente</h5>

        <?php if ($pendingInvoices): ?>
            <ul class="list-group list-group-flush rounded-1 border-0" style="max-height: 400px; overflow-y: auto;">
    <?php foreach ($pendingInvoices as $inv): 
        $status_class = match($inv['status']) {
            'en attente de paiement' => 'bg-danger',
            'payée' => 'bg-success',
            'annulée' => 'bg-warning',
            default => 'bg-secondary'
        };
    ?>
    <li class="list-group-item d-flex justify-content-between align-items-center rounded-1"
        style="width: 98%; margin: auto; transition: transform 0.2s; padding: 0.75rem 1rem; border: none; border-radius: 0;">
        <div>
            <strong><?= htmlentities($inv['invoice_number']) ?></strong><br>
            <small>Échéance : <?= htmlentities($inv['due_date']) ?></small><br>
            <?= htmlentities($inv['firstname'].' '.$inv['lastname']) ?>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge <?= $status_class ?> py-1 px-2"><?= ucfirst($inv['status']) ?></span>
            <a href="/projet/views/administrator/download_invoice.php?id=<?= $inv['id_invoice'] ?>"
               class="btn3 btn-sm d-flex justify-content-center align-items-center text-white rounded-1"
               style="width:40px; height:40px;"
               title="Télécharger la facture">
                <i class="fa-solid fa-file-pdf fa-beat"></i>
            </a>
        </div>
    </li>
    <?php endforeach; ?>
</ul>
        <?php else: ?>
            <p>Aucune facture en attente</p>
        <?php endif; ?>
    </div>
</div>

      <!-- MESSAGES -->
<div class="col-md-6">
    <div class="card shadow-sm p-4 mb-4">
        <h5 class="text-orange-fonce">Messages non lus</h5>

        <?php if ($messages): ?>
            <ul class="list-group list-group-flush rounded-1 border-0" style="max-height: 400px; overflow-y: auto;">
                <?php foreach ($messages as $msg): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center rounded-1"
                    style="width: 98%; margin: auto; transition: transform 0.2s; padding: 0.75rem 1rem; border-radius: 0;">
                    <div>
                        <strong><?= htmlentities($msg['first_name'].' '.$msg['last_name']) ?></strong><br>
                        <?= htmlentities($msg['subject']) ?><br>
                        <small><?= htmlentities($msg['created_at']) ?></small>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Aucun message non lu</p>
        <?php endif; ?>
    </div>
</div>

    </div>

    <div class="row">

        <!-- DERNIERS DEVIS -->
        <div class="col-md-6">
            <div class="card shadow-sm p-4 mb-4">
                <h5 class="text-orange-fonce">Derniers devis</h5>

                <?php if ($quotes): ?>
                    <ul class="list-group">
                        <?php foreach ($quotes as $q):
                            $status_class = match($q['status']) {
                                'en attente' => 'bg-info',
                                'signé' => 'bg-success',
                                'annulé' => 'bg-warning',
                                default => 'bg-secondary'
                            };
                        ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                            <div>
                                <strong><?= htmlentities($q['quote_number']) ?></strong><br>
                                <small><?= htmlentities($q['quote_date']) ?></small>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge <?= $status_class ?> py-1 px-2"><?= ucfirst($q['status']) ?></span>
                                <a href="/projet/views/administrator/download_quotation.php?id=<?= $q['id_quote'] ?>"
                                   class="btn3 btn-sm d-flex justify-content-center align-items-center text-white rounded-1"
                                   style="width:40px; height:40px;"
                                   title="Télécharger le devis">
                                    <i class="fa-solid fa-file-pdf fa-beat"></i>
                                </a>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>Aucun devis</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- DERNIÈRES FACTURES -->
        <div class="col-md-6">
            <div class="card shadow-sm p-4 mb-4">
                <h5 class="text-orange-fonce">Dernières factures</h5>

                <?php if ($invoices): ?>
                    <ul class="list-group">
                        <?php foreach ($invoices as $inv):
                            $status_class = match($inv['status']) {
                                'en attente de paiement' => 'bg-danger',
                                'payée' => 'bg-success',
                                'annulée' => 'bg-warning',
                                default => 'bg-secondary'
                            };
                        ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center p-3 ">
                            <div>
                                <strong><?= htmlentities($inv['invoice_number']) ?></strong><br>
                                <small><?= htmlentities($inv['invoice_date']) ?></small>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge <?= $status_class ?> py-1 px-2"><?= ucfirst($inv['status']) ?></span>
                                <a href="/projet/views/administrator/download_invoice.php?id=<?= $inv['id_invoice'] ?>"
                                   class="btn3 btn-sm d-flex justify-content-center align-items-center text-white rounded-1"
                                   style="width:40px; height:40px;"
                                   title="Télécharger la facture">
                                    <i class="fa-solid fa-file-pdf fa-beat"></i>
                                </a>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>Aucune facture</p>
                <?php endif; ?>
            </div>
        </div>

    </div>

</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layout.php';
?>