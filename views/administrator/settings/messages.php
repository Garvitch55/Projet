<?php
require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../../head.php';

// -------------------------
// Vérification de connexion
if (!isset($_SESSION['id'])) {
    header("Location: login.php?status=danger&message=Veuillez vous connecter.");
    exit;
}

// Vérification du rôle administrateur
if ($_SESSION['role'] !== 'administrateur') {
    header("Location: ../../../index.php?status=danger&message=Accès refusé.");
    exit;
}

$title = "Tous les messages";

// -------------------------
// Inclure le controller pour récupérer $all_messages
require __DIR__ . '/../../../controller/administrator/message_ctrl.php';

// -------------------------
// Construction du contenu
ob_start();
?>

<section class="m-4">

    <div class="d-flex justify-content-between align-items-center mt-3">
        <h1 class="text-orange-fonce mb-4">Liste des messages</h1>
        <a href="/projet/views/administrator/parameter.php" class="btn text-white">
            <i class="bi bi-arrow-left me-2"></i> Retour
        </a>
    </div>

    <?php if (!empty($all_messages)): ?>
        <ul class="list-group">
            <?php foreach ($all_messages as $msg): 
                $badge_class = $msg['is_read'] ? 'bg-success' : 'bg-danger';
                $badge_text = $msg['is_read'] ? 'Lu' : 'Non lu';
                $id = $msg['id_contact'];
            ?>
            <li class="list-group-item d-flex justify-content-between align-items-start position-relative">
                <!-- Contenu cliquable pour ouvrir la page du message -->
                <div class="flex-grow-1">
                    <a href="views/administrator/settings/view_message.php?id=<?= $id ?>&action=read" class="text-decoration-none d-block">
                        <p class="fw-bold m-0"><?= htmlentities($msg['first_name'].' '.$msg['last_name']) ?></p>
                        <p class="m-0"><?= htmlentities($msg['subject']) ?></p>
                        <p class="m-0"><?= htmlentities($msg['created_at']) ?></p>
                    </a>
                </div>

                <div class="d-flex gap-2 align-items-center position-absolute top-0 end-0 m-2">
                    <!-- Badge "Lu / Non lu" -->
                    <?php
                        $badge_display = ($badge_text === 'Non lu') ? "Non<br>lu" : $badge_text;
                    ?>
                    <span class="badge <?= $badge_class ?> rounded-circle d-flex flex-column justify-content-center align-items-center text-center" 
                          style="width:40px; height:40px; font-size:0.6rem; line-height:1rem;">
                        <?= $badge_display ?>
                    </span>

                    <!-- Bouton supprimer (ouvre le modal) -->
                    <a href="#" 
                       class="btn3 btn-sm rounded-circle d-flex justify-content-center align-items-center text-white"
                       style="width: 40px; height: 40px;"
                       data-bs-toggle="modal"
                       data-bs-target="#deleteModal<?= $id ?>">
                       <i class="bi bi-x-lg d-flex"></i>
                    </a>
                </div>
            </li>

            <!-- Modal de confirmation pour supprimer le message -->
            <div class="modal fade" id="deleteModal<?= $id ?>" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-gris-fonce text-white rounded-1">
                            <h5 class="modal-title">Confirmation</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            Êtes-vous sûr de vouloir supprimer ce message de <strong><?= htmlentities($msg['first_name'].' '.$msg['last_name']) ?></strong> ?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn text-white" data-bs-dismiss="modal">Annuler</button>
                            <form method="POST" action="controller/administrator/message_ctrl.php?action=delete">
                                <input type="hidden" name="id_contact" value="<?= $id ?>">
                                <button type="submit" class="btn btn-danger text-white">Supprimer</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Aucun message trouvé.</p>
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
                <a class="page-link bg-orange-fonce text-white" href="/projet/views/administrator/settings/messages.php?page=1">&laquo;&laquo;</a>
            <?php endif; ?>
        </li>

        <!-- Précédent -->
        <li class="page-item">
            <?php if($currentPage == 1): ?>
                <span class="page-link bg-gris-fonce text-white">&laquo;</span>
            <?php else: ?>
                <a class="page-link bg-orange-fonce text-white" href="/projet/views/administrator/settings/messages.php?page=<?= max(1, $currentPage - 1) ?>">&laquo;</a>
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
                    <a class="page-link bg-gris-fonce text-white" href="/projet/views/administrator/settings/messages.php?page=<?= $page ?>"><?= $page ?></a>
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
                <a class="page-link bg-orange-fonce text-white" href="/projet/views/administrator/settings/messages.php?page=<?= min($totalPages, $currentPage + 1) ?>">&raquo;</a>
            <?php endif; ?>
        </li>

        <!-- Fin -->
        <li class="page-item">
            <?php if($currentPage == $totalPages): ?>
                <span class="page-link bg-gris-fonce text-white">&raquo;&raquo;</span>
            <?php else: ?>
                <a class="page-link bg-orange-fonce text-white" href="/projet/views/administrator/settings/messages.php?page=<?= $totalPages ?>">&raquo;&raquo;</a>
            <?php endif; ?>
        </li>

    </ul>
</nav>
<?php endif; ?>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<?php
$content = ob_get_clean();

// ----------------- INCLURE LAYOUT -----------------
require __DIR__ . '/../../../layout.php';