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
$title = "Liste des devis";

// -------------------------
// Pagination
// -------------------------
$pdo = getPDO();
$perPage = 10;
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($currentPage - 1) * $perPage;

// Compter le total des devis
$totalQuotes = $pdo->query("SELECT COUNT(*) FROM quotes")->fetchColumn();
$totalPages = ceil($totalQuotes / $perPage);

// Récupérer les devis avec infos client
$stmt = $pdo->prepare("
    SELECT q.id_quote, q.quote_number, q.quote_date, q.status, q.total_ht, q.total_vat, q.total_ttc,
           c.firstname, c.lastname
    FROM quotes q
    JOIN gestion_client c ON q.client_id = c.id_client
    ORDER BY q.created_at DESC
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$all_quotes = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
        <h1 class="text-orange-fonce mb-4">Liste des devis</h1>
        <a href="/projet/views/administrator/settings/create_client.php" class="btn me-2 text-white">+</a>
    </div>

    <?php if (!empty($all_quotes)): ?>
        <ul class="list-group mb-3">
    <?php foreach ($all_quotes as $quote):
        $id = $quote['id_quote'];
        $status_class = match($quote['status']) {
    'en attente' => 'bg-warning',
    'signé' => 'bg-success',
    'annulé' => 'bg-danger',
            default => 'bg-secondary'
        };
    ?>
    <li class="list-group-item d-flex justify-content-between align-items-start position-relative">
        <div class="flex-grow-1">
            <a href="view_quotation.php?id=<?= $id ?>" class="text-decoration-none d-block">
                <p class="fw-bold m-0"><?= htmlentities($quote['firstname'].' '.$quote['lastname']) ?></p>
                <p class="m-0">Devis #: <?= htmlentities($quote['quote_number']) ?></p>
                <p class="m-0">Date: <?= htmlentities($quote['quote_date']) ?></p>
                <p class="m-0">Total TTC: <?= number_format($quote['total_ttc'],2,',',' ') ?> €</p>
            </a>
        </div>

        <!-- Badge statut -->
        <span class="badge <?= $status_class ?> rounded-pill align-self-center"><?= ucfirst($quote['status']) ?></span>

        <!-- Bouton supprimer avec modal -->
        <a href="#" 
           class="btn4 btn-sm rounded-circle d-flex justify-content-center align-items-center m-2 position-absolute top-0 end-0"
           style="width:40px;height:40px;"
           data-bs-toggle="modal"
           data-bs-target="#deleteModal<?= $id ?>">
            <i class="bi bi-x-lg"></i>
        </a>
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
                    Êtes-vous sûr de vouloir supprimer le devis
                    <strong><?= htmlentities($quote['quote_number']) ?></strong> du client
                    <strong><?= htmlentities($quote['firstname'].' '.$quote['lastname']) ?></strong> ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn text-white" data-bs-dismiss="modal">Annuler</button>
                    <form method="POST" action="/projet/controller/administrator/delete_quotation_ctrl.php">
                        <input type="hidden" name="id_quote" value="<?= $id ?>">
                        <button type="submit" class="btn btn-danger text-white">Supprimer</button>
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
                <?php
                // Lien vers première page
                if($currentPage == 1): ?>
                    <li class="page-item"><span class="page-link bg-gris-fonce text-white">&laquo;&laquo;</span></li>
                <?php else: ?>
                    <li class="page-item"><a class="page-link bg-orange-fonce text-white" href="views/administrator/<?= $currentUrl ?>?page=1">&laquo;&laquo;</a></li>
                <?php endif; ?>

                <!-- Lien vers précédent -->
                <?php if($currentPage == 1): ?>
                    <li class="page-item"><span class="page-link bg-gris-fonce text-white">&laquo;</span></li>
                <?php else: ?>
                    <li class="page-item"><a class="page-link bg-orange-fonce text-white" href="views/administrator/<?= $currentUrl ?>?page=<?= $currentPage - 1 ?>">&laquo;</a></li>
                <?php endif; ?>

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

                <!-- Lien vers suivant -->
                <?php if($currentPage == $totalPages): ?>
                    <li class="page-item"><span class="page-link bg-gris-fonce text-white">&raquo;</span></li>
                <?php else: ?>
                    <li class="page-item"><a class="page-link bg-orange-fonce text-white" href="views/administrator/<?= $currentUrl ?>?page=<?= $currentPage + 1 ?>">&raquo;</a></li>
                <?php endif; ?>

                <!-- Lien vers dernière page -->
                <?php if($currentPage == $totalPages): ?>
                    <li class="page-item"><span class="page-link bg-gris-fonce text-white">&raquo;&raquo;</span></li>
                <?php else: ?>
                    <li class="page-item"><a class="page-link bg-orange-fonce text-white" href="views/administrator/<?= $currentUrl ?>?page=<?= $totalPages ?>">&raquo;&raquo;</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>

    <?php else: ?>
        <p>Aucun devis trouvé.</p>
    <?php endif; ?>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layout.php';
