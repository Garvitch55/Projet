<?php
require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../../head.php';

// -------------------------
// Vérification de connexion
// -------------------------
if (!isset($_SESSION['id'])) {
    header("Location: login.php?status=danger&message=Veuillez vous connecter.");
    exit;
}

// Vérification du rôle administrateur
if ($_SESSION['role'] !== 'administrateur') {
    header("Location: ../../index.php?status=danger&message=Accès refusé.");
    exit;
}

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

// -------------------------
// Récupérer les devis avec infos client
// -------------------------
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

// -------------------------
// Construction du contenu
// -------------------------
ob_start();
?>

<section class="m-4">

    <div class="d-flex justify-content-between align-items-center mt-3">
        <h1 class="text-orange-fonce mb-4">Liste des devis</h1>
        <a href="/projet/views/administrator/parameter.php" class="btn text-white">
            <i class="bi bi-arrow-left me-2"></i> Retour
        </a>
    </div>

    <?php if (!empty($all_quotes)): ?>
        <ul class="list-group">
            <?php foreach ($all_quotes as $quote):
                $id = $quote['id_quote'];
                $status_class = match($quote['status']) {
                    'pending' => 'bg-warning',
                    'signed' => 'bg-success',
                    'cancelled' => 'bg-danger',
                    default => 'bg-secondary'
                };
            ?>
            <li class="list-group-item d-flex justify-content-between align-items-start position-relative">
                <div class="flex-grow-1">
                    <a href="view_quotation.php?id=<?= $id ?>" class="text-decoration-none d-block">
                        <p class="fw-bold m-0"><?= htmlentities($quote['firstname'] . ' ' . $quote['lastname']) ?></p>
                        <p class="m-0">Devis #: <?= htmlentities($quote['quote_number']) ?></p>
                        <p class="m-0">Date: <?= htmlentities($quote['quote_date']) ?></p>
                        <p class="m-0">Total TTC: <?= number_format($quote['total_ttc'], 2, ',', ' ') ?> €</p>
                    </a>
                </div>

                <div class="d-flex gap-2 align-items-center position-absolute top-0 end-0 m-2">
                    <span class="badge <?= $status_class ?> rounded-pill">
                        <?= ucfirst($quote['status']) ?>
                    </span>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Aucun devis trouvé.</p>
    <?php endif; ?>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <nav class="mt-3">
        <ul class="pagination justify-content-center">
            <!-- Début -->
            <li class="page-item">
                <?php if($currentPage == 1): ?>
                    <span class="page-link bg-gris-fonce text-white">&laquo;&laquo;</span>
                <?php else: ?>
                    <a class="page-link bg-orange-fonce text-white" href="?page=1">&laquo;&laquo;</a>
                <?php endif; ?>
            </li>
            <!-- Précédent -->
            <li class="page-item">
                <?php if($currentPage == 1): ?>
                    <span class="page-link bg-gris-fonce text-white">&laquo;</span>
                <?php else: ?>
                    <a class="page-link bg-orange-fonce text-white" href="?page=<?= $currentPage - 1 ?>">&laquo;</a>
                <?php endif; ?>
            </li>

            <?php
            $window = 5;
            $start = max(1, $currentPage - 2);
            $end = min($totalPages, $start + $window - 1);
            if ($end - $start < $window - 1) {
                $start = max(1, $end - $window + 1);
            }
            ?>

            <?php for ($page = $start; $page <= $end; $page++): ?>
                <li class="page-item">
                    <?php if($page == $currentPage): ?>
                        <span class="page-link bg-orange-fonce text-white"><?= $page ?></span>
                    <?php else: ?>
                        <a class="page-link bg-gris-fonce text-white" href="?page=<?= $page ?>"><?= $page ?></a>
                    <?php endif; ?>
                </li>
            <?php endfor; ?>

            <!-- Suivant -->
            <li class="page-item">
                <?php if($currentPage == $totalPages): ?>
                    <span class="page-link bg-gris-fonce text-white">&raquo;</span>
                <?php else: ?>
                    <a class="page-link bg-orange-fonce text-white" href="?page=<?= $currentPage + 1 ?>">&raquo;</a>
                <?php endif; ?>
            </li>
            <!-- Fin -->
            <li class="page-item">
                <?php if($currentPage == $totalPages): ?>
                    <span class="page-link bg-gris-fonce text-white">&raquo;&raquo;</span>
                <?php else: ?>
                    <a class="page-link bg-orange-fonce text-white" href="?page=<?= $totalPages ?>">&raquo;&raquo;</a>
                <?php endif; ?>
            </li>
        </ul>
    </nav>
    <?php endif; ?>

</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../../layout.php';
?>