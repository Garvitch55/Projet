<?php

require_once __DIR__ . '/../../config.php';

// -------------------------
// Sécurité : utilisateur connecté
// -------------------------
if (!isset($_SESSION['id'])) {
    header("Location: ../login.php?status=danger&message=Veuillez vous connecter.");
    exit;
}

// -------------------------
// Vérification du rôle client
// -------------------------
if ($_SESSION['role'] !== 'client') {
    header("Location: ../../index.php?status=danger&message=Accès refusé.");
    exit;
}

require_once __DIR__ . '/../../head.php';
$title = "Mes devis";

// -------------------------
// Pagination
// -------------------------
$pdo = getPDO();
$client_id = $_SESSION['id'];

$perPage = 10;
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($currentPage - 1) * $perPage;

// Total devis pour ce client
$stmt = $pdo->prepare("SELECT COUNT(*) FROM quotes WHERE client_id = :client_id");
$stmt->execute([':client_id' => $client_id]);
$totalQuotes = $stmt->fetchColumn();

$totalPages = ceil($totalQuotes / $perPage);

// -------------------------
// Récupération des devis avec paramètres nommés
// -------------------------
$stmt = $pdo->prepare("
    SELECT id_quote, quote_number, quote_date, status, total_ttc
    FROM quotes
    WHERE client_id = :client_id
    ORDER BY created_at DESC
    LIMIT :limit OFFSET :offset
");

// PDO n'accepte que PDO::PARAM_INT pour LIMIT et OFFSET
$stmt->bindValue(':client_id', $client_id, PDO::PARAM_INT);
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();
$quotes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// URL de la page pour la pagination
$currentUrl = basename($_SERVER['PHP_SELF']);

ob_start();
?>
<?php
// -------------------------
// Notifications
// -------------------------
ob_start();
require ROOT . "notification.php";
$notification = ob_get_clean();
?>

<section class="m-4">

    <div class="d-flex justify-content-between align-items-center mt-3">
        <h1 class="text-orange-fonce mb-4">Mes devis</h1>
    </div>

    <?php if (!empty($quotes)): ?>
        <ul class="list-group mb-3">

        <?php foreach ($quotes as $quote):

            $status_class = match($quote['status']) {
                'en attente' => 'bg-info',
                'signé' => 'bg-success',
                'annulé' => 'bg-danger',
                default => 'bg-secondary'
            };
        ?>

<li class="list-group-item position-relative p-3">

    <div class="flex-grow-1">
        <p class="fw-bold m-0">Devis: <?= htmlentities($quote['quote_number']) ?></p>
        <p class="m-0">Date: <?= htmlentities($quote['quote_date']) ?></p>
        <p class="m-0">Total TTC: <?= number_format($quote['total_ttc'],2,',',' ') ?> €</p>
    </div>

    <!-- Bouton téléchargement + badge alignés verticalement à droite -->
    <div class="position-absolute top-0 end-0 text-end m-3 d-flex flex-column align-items-end">

        <!-- Icône PDF -->
        <a href="/projet/views/customer/download_quotation.php?id=<?= $quote['id_quote'] ?>"
           class="btn3 btn-sm d-flex justify-content-center align-items-center text-white mb-2"
           style="width:40px; height:40px;"
           title="Télécharger le devis">
            <i class="fa-solid fa-file-pdf fa-beat"></i>
        </a>

        <!-- Badge -->
        <span class="badge <?= $status_class ?> rounded-pill">
            <?= ucfirst($quote['status']) ?>
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
        <p>Aucun devis trouvé.</p>
    <?php endif; ?>

</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layout.php';
?>