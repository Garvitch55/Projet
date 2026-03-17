<?php

require_once __DIR__ . '/../../config.php';

// On récupère le type d'utilisateur depuis l'URL
$type = $_GET['type'] ?? 'administrateur';

// Pas connecté → dehors
if (!isset($_SESSION['id'])) {
    header("Location: login.php?status=danger&message=Veuillez vous connecter.");
    exit;
}

// Pas admin → dehors
if ($_SESSION['role'] !== 'administrateur') {
    header("Location: ../../index.php?status=danger&message=Accès refusé.");
    exit;
}

require_once __DIR__ . '/../../head.php';    // head_with_title
$title = "Clients";

// ------------------ Controller ------------------
$all_clients = [];
$error = '';

try {
    $pdo = getPDO();

    // Filtre alphabétique
    $letter = $_GET['letter'] ?? 'ALL';

    // Pagination
    $currentPage = max(1, (int)($_GET['page'] ?? 1));
    $perPage = 10;
    $offset = ($currentPage - 1) * $perPage;

    if ($letter !== 'ALL') {
        // Comptage total pour cette lettre
        $countSql = "SELECT COUNT(*) FROM gestion_client WHERE lastname LIKE :letter";
        $countStmt = $pdo->prepare($countSql);
        $countStmt->execute(['letter' => $letter . '%']);
        $totalClients = (int)$countStmt->fetchColumn();

        // Clients pour la page
        $sql = "SELECT * FROM gestion_client 
                WHERE lastname LIKE :letter
                ORDER BY lastname ASC
                LIMIT :limit OFFSET :offset";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':letter', $letter . '%', PDO::PARAM_STR);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
    } else {
        // Comptage total
        $totalClients = (int)$pdo->query("SELECT COUNT(*) FROM gestion_client")->fetchColumn();

        $sql = "SELECT * FROM gestion_client 
                ORDER BY lastname ASC
                LIMIT :limit OFFSET :offset";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
    }

    $all_clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $totalPages = ceil($totalClients / $perPage);

} catch (PDOException $e) {
    $error = $e->getMessage();
    $all_clients = [];
    $totalPages = 1;
}

// ------------------ View ------------------
ob_start();

?>
<?php
ob_start();
require ROOT . "notification.php";
$notification = ob_get_clean();
?>
<section class="m-4">

<div class="d-flex justify-content-between align-items-center mt-3">
    <h1 class="text-orange-fonce mb-4">Liste des clients</h1>
    <div>
        <a href="/projet/views/administrator/create_customer.php" class="btn me-2 text-white"><i class="fa-solid fa-plus fa-beat"></i></a>
    </div>
</div>

<!-- MENU ALPHABÉTIQUE -->
<div class="mb-3 d-flex justify-content-between align-items-center mt-3">
    <div>
    <span>Filtrer par lettre : </span>
</div>
 <div class=" rounded-3">
    <?php
        $letters = range('A', 'Z');
    ?>
    <a href="/projet/views/administrator/customer.php?letter=ALL" 
       class="btn-alpha btn-sm text-white rounded-start <?= $letter === 'ALL' ? 'active' : '' ?>">
        Tous
    </a>
    <?php foreach ($letters as $l): ?>
        <a href="/projet/views/administrator/customer.php?letter=<?= $l ?>" 
           class="btn-alpha btn-sm text-white bg-gris-fonce <?= $letter === $l ? 'active' : '' ?>          <?= $letter === $l ? 'active bg-orange-fonce' : 'bg-gris-fonce' ?>
          <?= $l === 'Z' ? 'rounded-end' : '' ?>">
            <?= $l ?>
        </a>
    <?php endforeach; ?>
    </div>
</div>





<?php if (!empty($all_clients)): ?>
<ul class="list-group text-white">
    <?php foreach ($all_clients as $client):
        $id = $client['id_client'];
    ?>
<li class="list-group-item position-relative">

    <!-- Infos principales du client -->
    <div class="flex-grow-1">
        <a href="/projet/views/administrator/view_customer.php?id=<?= $id ?>" class="text-decoration-none d-block">
            <p class="fw-bold m-0"><?= htmlentities($client['firstname'].' '.$client['lastname']) ?></p>
            <p class="m-0"><?= htmlentities($client['email']) ?></p>
            <p class="m-0"><?= htmlentities($client['phone']) ?></p>
        </a>
    </div>

    <!-- Conteneur boutons aligné à droite -->
    <div class="position-absolute top-0 end-0 text-end m-2">
        <div class="d-flex gap-2 justify-content-end mb-2">

            <!-- Bouton View -->
            <a href="/projet/views/administrator/view_customer.php?id=<?= $id ?>"
               class="btn3 btn-sm d-flex justify-content-center align-items-center rounded-1 text-white"
               style="width:40px; height:40px;"
               title="Voir le client">
                <i class="fa-solid fa-eye fa-beat"></i>
            </a>

            <!-- Bouton Modifier -->
            <a href="/projet/views/administrator/update_customer.php?id=<?= $id ?>"
               class="btn3 btn-sm d-flex justify-content-center align-items-center rounded-1 text-white"
               style="width:40px; height:40px;"
               title="Modifier le client">
                <i class="fa-solid fa-pen-to-square fa-beat"></i>
            </a>

            <!-- Bouton Supprimer -->
            <a href="#"
               class="btn3 btn-sm rounded-circle d-flex justify-content-center align-items-center text-white"
               style="width:40px;height:40px;"
               data-bs-toggle="modal"
               data-bs-target="#deleteModal<?= $id ?>"
               title="Supprimer le client">
                <i class="bi bi-x-lg"></i>
            </a>
        </div>

        <!-- Badge rôle ou statut -->
        <div>
            <span class="badge bg-success rounded-pill"><?= ucfirst($client['role'] ?? 'client') ?></span>
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
                    Êtes-vous sûr de vouloir supprimer le compte client
                    <strong><?= htmlentities($client['firstname'].' '.$client['lastname']) ?></strong> ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn text-white" data-bs-dismiss="modal">Annuler</button>
                    <form method="POST" action="/projet/controller/administrator/delete_customer_ctrl.php">
                        <input type="hidden" name="id_client" value="<?= $id ?>">
                        <button type="submit" class="btn text-white">Supprimer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php endforeach; ?>
</ul>

<!-- Pagination améliorée -->
<?php if ($totalPages > 1): ?>
<nav class="mt-3">
    <ul class="pagination justify-content-center">

        <!-- Double flèche Début -->
        <li class="page-item">
            <?php if($currentPage == 1): ?>
                <span class="page-link bg-gris-fonce text-white">&laquo;&laquo;</span>
            <?php else: ?>
                <a class="page-link bg-orange-fonce text-white" href="/projet/views/administrator/customer.php?letter=<?= $letter ?>&page=1">&laquo;&laquo;</a>
            <?php endif; ?>
        </li>

        <!-- Flèche Précédent -->
        <li class="page-item">
            <?php if($currentPage == 1): ?>
                <span class="page-link bg-gris-fonce text-white">&laquo;</span>
            <?php else: ?>
                <a class="page-link bg-orange-fonce text-white" href="/projet/views/administrator/customer.php?letter=<?= $letter ?>&page=<?= max(1, $currentPage - 1) ?>">&laquo;</a>
            <?php endif; ?>
        </li>

        <?php
        // Fenêtre de pages (max 5)
        $window = 5;
        $start = max(1, $currentPage - 2);
        $end = min($totalPages, $start + $window - 1);
        if ($end - $start < $window - 1) {
            $start = max(1, $end - $window + 1);
        }
        ?>

        <!-- "..." avant les pages -->
        <?php if ($start > 1): ?>
            <li class="page-item disabled"><span class="page-link bg-light text-dark">...</span></li>
        <?php endif; ?>

        <!-- Pages -->
        <?php for ($page = $start; $page <= $end; $page++): ?>
            <li class="page-item">
                <?php if($page == $currentPage): ?>
                    <span class="page-link bg-orange-fonce text-white"><?= $page ?></span>
                <?php else: ?>
                    <a class="page-link bg-gris-fonce text-white" href="/projet/views/administrator/customer.php?letter=<?= $letter ?>&page=<?= $page ?>"><?= $page ?></a>
                <?php endif; ?>
            </li>
        <?php endfor; ?>

        <!-- "..." après les pages -->
        <?php if ($end < $totalPages): ?>
            <li class="page-item disabled"><span class="page-link bg-light text-dark">...</span></li>
        <?php endif; ?>

        <!-- Flèche Suivant -->
        <li class="page-item">
            <?php if($currentPage == $totalPages): ?>
                <span class="page-link bg-gris-fonce text-white">&raquo;</span>
            <?php else: ?>
                <a class="page-link bg-orange-fonce text-white" href="/projet/views/administrator/customer.php?letter=<?= $letter ?>&page=<?= min($totalPages, $currentPage + 1) ?>">&raquo;</a>
            <?php endif; ?>
        </li>

        <!-- Double flèche Fin -->
        <li class="page-item">
            <?php if($currentPage == $totalPages): ?>
                <span class="page-link bg-gris-fonce text-white">&raquo;&raquo;</span>
            <?php else: ?>
                <a class="page-link bg-orange-fonce text-white" href="/projet/views/administrator/customer.php?letter=<?= $letter ?>&page=<?= $totalPages ?>">&raquo;&raquo;</a>
            <?php endif; ?>
        </li>

    </ul>
</nav>
<?php endif; ?>

<?php else: ?>
<p>Aucun client trouvé.</p>
<?php endif; ?>

</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layout.php';

