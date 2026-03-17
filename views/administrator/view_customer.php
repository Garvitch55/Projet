<?php
require_once __DIR__ . '/../../config.php';

// -------------------------
// Vérification connexion et rôle
// -------------------------
if (!isset($_SESSION['id'])) {
    header("Location: login.php?status=danger&message=Veuillez vous connecter.");
    exit;
}
if ($_SESSION['role'] !== 'administrateur') {
    header("Location: ../../index.php?status=danger&message=Accès refusé.");
    exit;
}

// -------------------------
// Récupérer l'ID du client
// -------------------------
$id_client = $_GET['id'] ?? null;
if (!$id_client) {
    header("Location: customer.php?status=danger&message=Client introuvable.");
    exit;
}

try {
    $pdo = getPDO();

    // -------------------------
    // Récupérer les infos du client
    // -------------------------
    $stmt = $pdo->prepare("SELECT * FROM gestion_client WHERE id_client = ?");
    $stmt->execute([$id_client]);
    $client = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$client) {
        header("Location: customer.php?status=danger&message=Client introuvable.");
        exit;
    }

    // -------------------------
    // Récupérer les devis du client
    // -------------------------
    $stmtQuotes = $pdo->prepare("SELECT * FROM quotes WHERE client_id = ? ORDER BY quote_date DESC");
    $stmtQuotes->execute([$id_client]);
    $quotes = $stmtQuotes->fetchAll(PDO::FETCH_ASSOC);

    // -------------------------
    // Récupérer les factures du client
    // -------------------------
    $stmtInvoices = $pdo->prepare("SELECT * FROM invoices WHERE client_id = ? ORDER BY invoice_date DESC");
    $stmtInvoices->execute([$id_client]);
    $invoices = $stmtInvoices->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erreur SQL : " . $e->getMessage());
}

require_once __DIR__ . '/../../head.php';
$title = "Détails du client : " . htmlentities($client['firstname'].' '.$client['lastname']);

ob_start();
?>

<section class="m-4">
    <div class="d-flex justify-content-between align-items-center mt-3">
        <h1 class="text-orange-fonce mb-4">Détails du client</h1>
        <a href="/projet/views/administrator/customer.php" class="btn text-white">
            <i class="bi bi-arrow-left me-2"></i> Retour
        </a>
    </div>
    <!-- Détails du client -->
    <div class="mb-4">
        <h3 class="text-gris-fonce text-decoration-underline">Coordonées: </h3>
        <div class="card bg-gris-fonce text-white p-3">
            <p><b>Nom :</b> <?= htmlentities($client['firstname'].' '.$client['lastname']) ?></p>
            <p><b>Email :</b> <?= htmlentities($client['email']) ?></p>
            <p><b>Téléphone :</b> <?= htmlentities($client['phone']) ?></p>
            <p><b>Adresse :</b> <?= htmlentities($client['rue'].' '.$client['cp'].' '.$client['ville']) ?></p>
        </div>
    </div>

    <!-- Liste des devis -->
    <div class="mb-4">
        <h3 class="text-gris-fonce text-decoration-underline">Devis du client:</h3>
        <?php if (!empty($quotes)): ?>
            <ul class="list-group text-white">
                <?php foreach ($quotes as $quote): ?>
                <li class="list-group-item position-relative">
                    <div class="flex-grow-1 p-3">
                        <p class="fw-bold m-0">Devis: <?= htmlentities($quote['quote_number']) ?></p>
                        <p class="m-0"><b>Date:</b> <?= htmlentities($quote['quote_date']) ?></p>
                        <p class="m-0"><b>Total TTC:</b> <?= number_format($quote['total_ttc'],2,',',' ') ?> €</p>
                    </div>

                    <!-- Boutons à droite -->
                    <div class="position-absolute top-0 end-0 text-end m-2 d-flex gap-2">
                        <a href="download_quotation.php?id=<?= $quote['id_quote'] ?>"
                           class="btn3 btn-sm d-flex justify-content-center align-items-center rounded-1 text-white"
                           style="width:40px;height:40px;"
                           title="Télécharger le devis">
                            <i class="fa-solid fa-file-pdf fa-beat"></i>
                        </a>
                        <a href="update_quote.php?id=<?= $quote['id_quote'] ?>"
                           class="btn3 btn-sm d-flex justify-content-center align-items-center rounded-1 text-white"
                           style="width:40px;height:40px;"
                           title="Modifier le devis">
                            <i class="fa-solid fa-pen-to-square fa-beat"></i>
                        </a>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Aucun devis trouvé pour ce client.</p>
        <?php endif; ?>
    </div>

    <!-- Liste des factures -->
    <div class="mb-4">
        <h3 class="text-gris-fonce text-decoration-underline">Factures du client:</h3>
        <?php if (!empty($invoices)): ?>
            <ul class="list-group text-white">
                <?php foreach ($invoices as $invoice): ?>
                <li class="list-group-item position-relative">
                    <div class="flex-grow-1  p-3">
                        <p class="fw-bold m-0">Facture: <?= htmlentities($invoice['invoice_number']) ?></p>
                        <p class="m-0"><b>Date:</b> <?= htmlentities($invoice['invoice_date']) ?></p>
                        <p class="m-0"><b>Échéance:</b> <?= htmlentities($invoice['due_date']) ?></p>
                        <p class="m-0"><b>Total TTC:</b> <?= number_format($invoice['total_ttc'],2,',',' ') ?> €</p>
                    </div>

                    <!-- Boutons à droite -->
                    <div class="position-absolute top-0 end-0 text-end m-2 d-flex gap-2">
                        <a href="download_invoice.php?id=<?= $invoice['id_invoice'] ?>"
                           class="btn3 btn-sm d-flex justify-content-center align-items-center rounded-1 text-white"
                           style="width:40px;height:40px;"
                           title="Télécharger la facture">
                            <i class="fa-solid fa-file-pdf fa-beat"></i>
                        </a>
                        <a href="update_invoice.php?id=<?= $invoice['id_invoice'] ?>"
                           class="btn3 btn-sm d-flex justify-content-center align-items-center rounded-1 text-white"
                           style="width:40px;height:40px;"
                           title="Modifier la facture">
                            <i class="fa-solid fa-pen-to-square fa-beat"></i>
                        </a>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Aucune facture trouvée pour ce client.</p>
        <?php endif; ?>
    </div>

</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layout.php';
?>