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

// Vérification admin
if ($_SESSION['role'] !== 'administrateur') {
    header("Location: ../../../index.php?status=danger&message=Accès refusé.");
    exit;
}

$title = "Gestion du personnel";

// -------------------------
// Pagination
// -------------------------
$pdo = getPDO();

$limit = 10;
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$currentPage = max(1, $currentPage);

$offset = ($currentPage - 1) * $limit;

// Total
$totalStmt = $pdo->query("SELECT COUNT(*) FROM gestion_personnel");
$totalItems = $totalStmt->fetchColumn();
$totalPages = ceil($totalItems / $limit);

// Données
$stmt = $pdo->prepare("
    SELECT * FROM gestion_personnel 
    ORDER BY firstname ASC 
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$staff = $stmt->fetchAll(PDO::FETCH_ASSOC);

// -------------------------
ob_start();
?>
<?php
ob_start();
require ROOT . "notification.php";
$notification = ob_get_clean();
?>

<section class="m-4">

    <div class="d-flex justify-content-between align-items-center mt-3">
        <h1 class="text-orange-fonce mb-4">Liste du personnel</h1>
        <div>
        <a href="/projet/views/administrator/settings/create_staff.php" class="btn me-2 text-white"><i class="fa-solid fa-plus fa-beat"></i></a>
        <a href="/projet/views/administrator/parameter.php" class="btn text-white">
            <i class="bi bi-arrow-left me-2"></i> Retour
        </a>
        </div>
    </div>

    <?php if (!empty($staff)): ?>
        <ul class="list-group">
            <?php foreach ($staff as $user): 
                $id = $user['id_personnel'];
                $isAdmin = $user['fonction'] === 'administrateur';

                $badge_class = $isAdmin ? 'bg-danger' : 'bg-success';
                $badge_text = $isAdmin ? 'Admin' : 'Employé';
            ?>
            <li class="list-group-item d-flex justify-content-between align-items-start position-relative">

                <!-- Infos -->
                <div class="flex-grow-1 p-3">
                    <p class="fw-bold m-0">
                        <?= htmlentities($user['firstname'].' '.$user['lastname']) ?>
                    </p>
                    <p class="m-0"><?= htmlentities($user['mail']) ?></p>
                    <p class="m-0"><?= htmlentities($user['phone']) ?></p>
                </div>

                <!-- Actions -->
                <div class="d-flex gap-2 align-items-center position-absolute top-0 end-0 m-2">

                    <!-- Badge -->
                    <span class="badge <?= $badge_class ?> rounded-circle d-flex justify-content-center align-items-center text-center"
                          style="width:40px; height:40px; font-size:0.6rem;">
                        <?= $badge_text ?>
                    </span>

                    <!-- Modifier -->
                    <a href="views/administrator/settings/update_staff.php?id=<?= $id ?>"
                       class="btn3 btn-sm d-flex justify-content-center align-items-center rounded-1 text-white"
                       style="width:40px; height:40px;">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>

                    <!-- Supprimer -->
                    <a href="#"
                       class="btn3 btn-sm rounded-circle d-flex justify-content-center align-items-center text-white"
                       style="width:40px;height:40px;"
                       data-bs-toggle="modal"
                       data-bs-target="#deleteModal<?= $id ?>">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </li>

            <!-- Modal -->
            <div class="modal fade" id="deleteModal<?= $id ?>" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-gris-fonce text-white rounded-1">
                            <h5 class="modal-title">Confirmation</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            Supprimer <strong><?= htmlentities($user['firstname'].' '.$user['lastname']) ?></strong> ?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn text-white" data-bs-dismiss="modal">Annuler</button>
                            <form method="POST" action="controller/administrator/delete_staff_ctrl.php?action=delete">
                                <input type="hidden" name="id_personnel" value="<?= $id ?>">
                                <button type="submit" class="btn text-white">Supprimer</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Aucun membre du personnel trouvé.</p>
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
                    <a class="page-link bg-orange-fonce text-white" href="/projet/views/administrator/settings/list_staff.php?page=1">&laquo;&laquo;</a>
                <?php endif; ?>
            </li>

            <!-- Précédent -->
            <li class="page-item">
                <?php if($currentPage == 1): ?>
                    <span class="page-link bg-gris-fonce text-white">&laquo;</span>
                <?php else: ?>
                    <a class="page-link bg-orange-fonce text-white" href="/projet/views/administrator/settings/list_staff.php?page=<?= max(1, $currentPage - 1) ?>">&laquo;</a>
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

            <?php if ($start > 1): ?>
                <li class="page-item disabled">
                    <span class="page-link bg-light text-dark">...</span>
                </li>
            <?php endif; ?>

            <?php for ($page = $start; $page <= $end; $page++): ?>
                <li class="page-item">
                    <?php if($page == $currentPage): ?>
                        <span class="page-link bg-orange-fonce text-white"><?= $page ?></span>
                    <?php else: ?>
                        <a class="page-link bg-gris-fonce text-white" href="/projet/views/administrator/settings/list_staff.php?page=<?= $page ?>"><?= $page ?></a>
                    <?php endif; ?>
                </li>
            <?php endfor; ?>

            <?php if ($end < $totalPages): ?>
                <li class="page-item disabled">
                    <span class="page-link bg-light text-dark">...</span>
                </li>
            <?php endif; ?>

            <!-- Suivant -->
            <li class="page-item">
                <?php if($currentPage == $totalPages): ?>
                    <span class="page-link bg-gris-fonce text-white">&raquo;</span>
                <?php else: ?>
                    <a class="page-link bg-orange-fonce text-white" href="/projet/views/administrator/settings/list_staff.php?page=<?= min($totalPages, $currentPage + 1) ?>">&raquo;</a>
                <?php endif; ?>
            </li>

            <!-- Fin -->
            <li class="page-item">
                <?php if($currentPage == $totalPages): ?>
                    <span class="page-link bg-gris-fonce text-white">&raquo;&raquo;</span>
                <?php else: ?>
                    <a class="page-link bg-orange-fonce text-white" href="/projet/views/administrator/settings/list_staff.php?page=<?= $totalPages ?>">&raquo;&raquo;</a>
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