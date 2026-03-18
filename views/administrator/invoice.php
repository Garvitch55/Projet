<?php

require_once __DIR__ . '/../../config.php';

// -------------------------
// Vérification de connexion
// -------------------------
if (!isset($_SESSION['id'])) {
    header("Location: login.php?status=danger&message=Veuillez vous connecter.");
    exit;
}

// -------------------------
// Vérification du rôle administrateur
// -------------------------
if ($_SESSION['role'] !== 'administrateur') {
    header("Location: ../../index.php?status=danger&message=Accès refusé.");
    exit;
}

require_once __DIR__ . '/../../head.php';
$title = "Liste des factures";

// -------------------------
// Pagination
// -------------------------
$pdo = getPDO();
$perPage = 10;
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($currentPage - 1) * $perPage;

// Compter le total des factures
$totalInvoices = $pdo->query("SELECT COUNT(*) FROM invoices")->fetchColumn();
$totalPages = ceil($totalInvoices / $perPage);

// Récupérer les factures avec infos client
$stmt = $pdo->prepare("
    SELECT i.id_invoice, i.invoice_number, i.invoice_date, i.due_date, i.status, i.total_ht, i.total_vat, i.total_ttc,
           c.firstname, c.lastname
    FROM invoices i
    JOIN gestion_client c ON i.client_id = c.id_client
    ORDER BY i.created_at DESC
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$all_invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Pour créer des liens corrects vers cette page
$currentUrl = basename($_SERVER['PHP_SELF']);
ob_start();
?>
<?php
ob_start();
require ROOT . "notification.php";
$notification = ob_get_clean();
?>

<section class="m-4">
    <div class="d-flex justify-content-between align-items-center mt-3">
        <h1 class="text-orange-fonce mb-4">Liste des factures</h1>
        <a href="/projet/views/administrator/create_invoice.php" class="btn me-2 text-white">
            <i class="fa-solid fa-plus fa-beat"></i>
        </a>
    </div>

    <?php if (!empty($all_invoices)): ?>
        <ul class="list-group mb-3">
        <?php foreach ($all_invoices as $invoice):
            $id = $invoice['id_invoice'];
            $status_class = match($invoice['status']) {
                'brouillon' => 'bg-secondary',
                'en attente de paiement' => 'bg-info',
                'payée' => 'bg-success',
                'annulée' => 'bg-danger',
                default => 'bg-secondary'
            };
        ?>
        <li class="list-group-item position-relative">
            <div class="flex-grow-1">
                <a href="views/administrator/download_invoice.php?id=<?= $id ?>" class="text-decoration-none d-block">
                    <p class="fw-bold m-0"><?= htmlentities($invoice['firstname'].' '.$invoice['lastname']) ?></p>
                    <p class="m-0">Facture #: <?= htmlentities($invoice['invoice_number']) ?></p>
                    <p class="m-0">Date: <?= htmlentities($invoice['invoice_date']) ?></p>
                    <p class="m-0">Échéance: <?= htmlentities($invoice['due_date']) ?></p>
                    <p class="m-0">Total TTC: <?= number_format($invoice['total_ttc'],2,',',' ') ?> €</p>
                </a>
            </div>

            <!-- Conteneur boutons + badge aligné à droite -->
            <div class="position-absolute top-0 end-0 text-end m-2">
                <div class="d-flex gap-2 justify-content-end mb-2">
                    <!-- Bouton Télécharger la facture -->
                    <a href="views/administrator/download_invoice.php?id=<?= $id ?>"
                       class="btn3 btn-sm d-flex justify-content-center align-items-center rounded-1 text-white"
                       style="width:40px; height:40px;"
                       title="Télécharger la facture">
                        <i class="fa-solid fa-file-pdf fa-beat"></i>
                    </a>

                    <!-- Bouton modifier -->
                    <a href="views/administrator/update_invoice.php?id=<?= $id ?>"
                       class="btn3 btn-sm d-flex justify-content-center align-items-center rounded-1 text-white"
                       style="width:40px; height:40px;"
                       title="Modifier la facture">
                        <i class="fa-solid fa-pen-to-square fa-beat"></i>
                    </a>

                    <!-- Bouton supprimer avec modal -->
                    <a href="#"
                       class="btn3 btn-sm rounded-circle d-flex justify-content-center align-items-center text-white"
                       style="width:40px;height:40px;"
                       data-bs-toggle="modal"
                       data-bs-target="#deleteModal<?= $id ?>">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>

                <!-- Badge statut -->
                <div>
                    <span class="badge <?= $status_class ?> rounded-pill"><?= ucfirst($invoice['status']) ?></span>
                </div>
            </div>
        </li>

        <!-- Modal suppression -->
        <div class="modal fade" id="deleteModal<?= $id ?>" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-gris-fonce text-white rounded-1">
                        <h5 class="modal-title">Confirmation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        Êtes-vous sûr de vouloir supprimer la facture
                        <strong><?= htmlentities($invoice['invoice_number']) ?></strong> du client
                        <strong><?= htmlentities($invoice['firstname'].' '.$invoice['lastname']) ?></strong> ?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn text-white" data-bs-dismiss="modal">Annuler</button>
                        <form method="POST" action="/projet/controller/administrator/delete_invoice_ctrl.php">
                            <input type="hidden" name="id_invoice" value="<?= $id ?>">
                            <button type="submit" class="btn text-white">Supprimer</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <?php endforeach; ?>
        </ul>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <nav>
            <ul class="pagination justify-content-center">
                <!-- Première page -->
                <li class="page-item">
                    <?php if($currentPage == 1): ?>
                        <span class="page-link bg-gris-fonce text-white">&laquo;&laquo;</span>
                    <?php else: ?>
                        <a class="page-link bg-orange-fonce text-white" href="views/administrator/<?= $currentUrl ?>?page=1">&laquo;&laquo;</a>
                    <?php endif; ?>
                </li>

                <!-- Précédent -->
                <li class="page-item">
                    <?php if($currentPage == 1): ?>
                        <span class="page-link bg-gris-fonce text-white">&laquo;</span>
                    <?php else: ?>
                        <a class="page-link bg-orange-fonce text-white" href="views/administrator/<?= $currentUrl ?>?page=<?= $currentPage - 1 ?>">&laquo;</a>
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
                            <a class="page-link bg-gris-fonce text-white" href="views/administrator/<?= $currentUrl ?>?page=<?= $page ?>"><?= $page ?></a>
                        <?php endif; ?>
                    </li>
                <?php endfor; ?>

                <!-- Suivant -->
                <li class="page-item">
                    <?php if($currentPage == $totalPages): ?>
                        <span class="page-link bg-gris-fonce text-white">&raquo;</span>
                    <?php else: ?>
                        <a class="page-link bg-orange-fonce text-white" href="views/administrator/<?= $currentUrl ?>?page=<?= $currentPage + 1 ?>">&raquo;</a>
                    <?php endif; ?>
                </li>

                <!-- Dernière page -->
                <li class="page-item">
                    <?php if($currentPage == $totalPages): ?>
                        <span class="page-link bg-gris-fonce text-white">&raquo;&raquo;</span>
                    <?php else: ?>
                        <a class="page-link bg-orange-fonce text-white" href="views/administrator/<?= $currentUrl ?>?page=<?= $totalPages ?>">&raquo;&raquo;</a>
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
