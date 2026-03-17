<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../head.php';

// -------------------------
// Sécurité : utilisateur connecté
// -------------------------
if (!isset($_SESSION['id'])) {
    header("Location: ../login.php?status=danger&message=Veuillez vous connecter.");
    exit;
}

// Vérification du rôle client
if ($_SESSION['role'] !== 'client') {
    header("Location: ../../index.php?status=danger&message=Accès refusé.");
    exit;
}

$title = "Mes factures";

try {
    $pdo = getPDO();
    $client_id = $_SESSION['id'];

    // -------------------------
    // Pagination
    // -------------------------
    $perPage = 10;
    $currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $offset = ($currentPage - 1) * $perPage;

    // Total factures pour ce client
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM invoices WHERE client_id = ?");
    $stmt->execute([$client_id]);
    $totalInvoices = $stmt->fetchColumn();
    $totalPages = ceil($totalInvoices / $perPage);

    // -------------------------
    // Récupération des factures
    // -------------------------
    $stmt = $pdo->prepare("
        SELECT id_invoice, invoice_number, invoice_date, due_date, status, total_ttc
        FROM invoices
        WHERE client_id = ?
        ORDER BY created_at DESC
        LIMIT ? OFFSET ?
    ");
    $stmt->bindValue(1, $client_id, PDO::PARAM_INT);
    $stmt->bindValue(2, $perPage, PDO::PARAM_INT);
    $stmt->bindValue(3, $offset, PDO::PARAM_INT);
    $stmt->execute();
    $invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // URL de la page pour pagination
    $currentUrl = basename($_SERVER['PHP_SELF']);

} catch (PDOException $e) {
    die("Erreur base de données : " . $e->getMessage());
}

// -------------------------
// Contenu HTML
// -------------------------
ob_start();
?>

<section class="m-4">

    <div class="d-flex justify-content-between align-items-center mt-3">
        <h1 class="text-orange-fonce mb-4">Mes factures</h1>
    </div>

    <?php if (!empty($invoices)): ?>
        <ul class="list-group mb-3">

        <?php foreach ($invoices as $invoice):

            $status_class = match($invoice['status']) {
                'brouillon' => 'bg-secondary',
                'en attente de paiement'   => 'bg-danger',
                'payée'     => 'bg-success',
                'annulée'   => 'bg-warning',
                default     => 'bg-secondary'
            };
        ?>

       <li class="list-group-item position-relative p-3">

    <div class="flex-grow-1">
        <p class="fw-bold m-0">Facture: <?= htmlentities($invoice['invoice_number']) ?></p>
        <p class="m-0">Date: <?= htmlentities($invoice['invoice_date']) ?></p>
        <p class="m-0">Échéance: <?= htmlentities($invoice['due_date']) ?></p>
        <p class="m-0">Total TTC: <?= number_format($invoice['total_ttc'],2,',',' ') ?> €</p>
    </div>

    <!-- Bouton téléchargement + badge -->
    <div class="position-absolute top-0 end-0 text-end m-3 d-flex flex-column align-items-end">

        <!-- Icône PDF -->
        <a href="/projet/views/customer/download_invoice.php?id=<?= $invoice['id_invoice'] ?>"
           class="btn3 btn-sm d-flex justify-content-center align-items-center text-white mb-2"
           style="width:40px; height:40px;"
           title="Télécharger la facture">
            <i class="fa-solid fa-file-pdf fa-beat"></i>
        </a>

        <!-- Badge -->
        <span class="badge <?= $status_class ?> rounded-pill">
            <?= ucfirst($invoice['status']) ?>
        </span>

    </div>

</li>

        <?php endforeach; ?>

        </ul>

        <!-- ================= PAGINATION ================= -->
        <?php if ($totalPages > 1): ?>
        <nav>
            <ul class="pagination justify-content-center">

                <!-- Première -->
                <li class="page-item">
                    <?php if($currentPage == 1): ?>
                        <span class="page-link bg-gris-fonce text-white">&laquo;&laquo;</span>
                    <?php else: ?>
                        <a class="page-link bg-orange-fonce text-white" href="views/customer/<?= $currentUrl ?>?page=1">&laquo;&laquo;</a>
                    <?php endif; ?>
                </li>

                <!-- Précédent -->
                <li class="page-item">
                    <?php if($currentPage == 1): ?>
                        <span class="page-link bg-gris-fonce text-white">&laquo;</span>
                    <?php else: ?>
                        <a class="page-link bg-orange-fonce text-white" href="views/customer/<?= $currentUrl ?>?page=<?= $currentPage - 1 ?>">&laquo;</a>
                    <?php endif; ?>
                </li>

                <!-- Pages -->
                <?php
                $window = 5;
                $start = max(1, $currentPage - 2);
                $end = min($totalPages, $start + $window - 1);
                if ($end - $start < $window - 1) {
                    $start = max(1, $end - $window + 1);
                }
                for($page=$start; $page <= $end; $page++):
                ?>
                    <li class="page-item">
                        <?php if($page == $currentPage): ?>
                            <span class="page-link bg-orange-fonce text-white"><?= $page ?></span>
                        <?php else: ?>
                            <a class="page-link bg-gris-fonce text-white" href="views/customer/<?= $currentUrl ?>?page=<?= $page ?>"><?= $page ?></a>
                        <?php endif; ?>
                    </li>
                <?php endfor; ?>

                <!-- Suivant -->
                <li class="page-item">
                    <?php if($currentPage == $totalPages): ?>
                        <span class="page-link bg-gris-fonce text-white">&raquo;</span>
                    <?php else: ?>
                        <a class="page-link bg-orange-fonce text-white" href="views/customer/<?= $currentUrl ?>?page=<?= $currentPage + 1 ?>">&raquo;</a>
                    <?php endif; ?>
                </li>

                <!-- Dernière -->
                <li class="page-item">
                    <?php if($currentPage == $totalPages): ?>
                        <span class="page-link bg-gris-fonce text-white">&raquo;&raquo;</span>
                    <?php else: ?>
                        <a class="page-link bg-orange-fonce text-white" href="views/customer/<?= $currentUrl ?>?page=<?= $totalPages ?>">&raquo;&raquo;</a>
                    <?php endif; ?>
                </li>
            </ul>
        </nav>
        <?php endif; ?>

    <?php else: ?>
        <p>Aucune facture trouvée.</p>
    <?php endif; ?>

</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layout.php';
?>