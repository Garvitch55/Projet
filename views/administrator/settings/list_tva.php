<?php

require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../../head.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php?status=danger&message=Veuillez vous connecter.");
    exit;
}

if ($_SESSION['role'] !== 'administrateur') {
    header("Location: ../../../index.php?status=danger&message=Accès refusé.");
    exit;
}

$title = "Liste des TVA";

$all_tva = [];
$error = '';

try {

    $pdo = getPDO();

    $currentPage = max(1, (int)($_GET['page'] ?? 1));
    $perPage = 10;
    $offset = ($currentPage - 1) * $perPage;

    $totalTva = (int)$pdo->query("SELECT COUNT(*) FROM tva")->fetchColumn();

    $sql = "
        SELECT *
        FROM tva
        ORDER BY rate ASC
        LIMIT :limit OFFSET :offset
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $all_tva = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $totalPages = ceil($totalTva / $perPage);

} catch (PDOException $e) {

    $error = $e->getMessage();
    $all_tva = [];
    $totalPages = 1;

}

ob_start();
?>
<?php
ob_start();
require ROOT . "notification.php";
$notification = ob_get_clean();
?>

<section class="m-4">

    <div class="d-flex justify-content-between align-items-center mt-3">

        <h1 class="text-orange-fonce mb-4">
            Liste des T.V.A.
        </h1>

        <div>

            <a href="/projet/views/administrator/settings/create_tva.php"
               class="btn me-2 text-white">
                <i class="fa-solid fa-plus fa-beat"></i>
            </a>

            <a href="/projet/views/administrator/parameter.php"
               class="btn text-white">
                <i class="bi bi-arrow-left me-2"></i>
                Retour
            </a>

        </div>

    </div>

<?php if (!empty($all_tva)) : ?>

    <ul class="list-group text-white">

    <?php foreach ($all_tva as $tva) :

        $id = $tva['id_tva'];

    ?>

        <li class="list-group-item d-flex justify-content-between align-items-start">

            <div class="flex-grow-1 p-3">

                <p class="fw-bold m-0">
                    <?= htmlentities($tva['name']) ?>
                </p>

                <p class="m-0">
                    Taux : <?= htmlentities($tva['rate']) ?> %
                </p>

                <p class="m-0">
                    <?= htmlentities($tva['description']) ?>
                </p>

            </div>

            <div class="d-flex gap-2 align-items-center">

    <!-- Bouton modifier -->
    <a href="/projet/views/administrator/settings/update_tva.php?id=<?= $id ?>"
       class="btn3 btn-sm d-flex justify-content-center align-items-center rounded-1 text-white"
       style="width:40px; height:40px;"
       title="Modifier le devis">
        <i class="fa-solid fa-pen-to-square fa-beat"></i>
    </a>


        <!-- Bouton supprimé -->
                <a href="#"
                   class="btn3 btn-sm rounded-circle d-flex justify-content-center align-items-center m-2"
                   style="width:40px;height:40px;"
                   data-bs-toggle="modal"
                   data-bs-target="#deleteModal<?= $id ?>">

                    <i class="bi bi-x-lg text-white"></i>

                </a>

            </div>

        </li>


        <div class="modal fade"
             id="deleteModal<?= $id ?>"
             tabindex="-1">

            <div class="modal-dialog modal-dialog-centered">

                <div class="modal-content">

                    <div class="modal-header bg-gris-fonce text-white">

                        <h5 class="modal-title">
                            Confirmation
                        </h5>

                        <button type="button"
                                class="btn-close"
                                data-bs-dismiss="modal">
                        </button>

                    </div>

                    <div class="modal-body">

                        Êtes-vous sûr de vouloir supprimer la TVA

                        <strong>
                            <?= htmlentities($tva['name']) ?>
                        </strong> ?

                    </div>

                    <div class="modal-footer">

                        <button type="button"
                                class="btn text-white"
                                data-bs-dismiss="modal">
                            Annuler
                        </button>

                        <form method="POST"
                              action="/projet/controller/administrator/delete_tva_ctrl.php">

                            <input type="hidden"
                                   name="id_tva"
                                   value="<?= $id ?>">

                            <button type="submit"
                                    class="btn text-white">
                                Supprimer
                            </button>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    <?php endforeach; ?>

    </ul>


<?php if ($totalPages > 1) : ?>

<nav class="mt-3">

    <ul class="pagination justify-content-center">

    <?php for ($page = 1; $page <= $totalPages; $page++) : ?>

        <li class="page-item">

        <?php if ($page == $currentPage) : ?>

            <span class="page-link bg-orange-fonce text-white">
                <?= $page ?>
            </span>

        <?php else : ?>

            <a class="page-link bg-gris-fonce text-white"
               href="/projet/views/administrator/settings/list_tva.php?page=<?= $page ?>">

                <?= $page ?>

            </a>

        <?php endif; ?>

        </li>

    <?php endfor; ?>

    </ul>

</nav>

<?php endif; ?>


<?php else : ?>

<p>Aucune TVA trouvée.</p>

<?php endif; ?>

</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<?php

$content = ob_get_clean();

require __DIR__ . '/../../../layout.php';