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

$title = "Liste des paiements";


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
            Liste des moyens de paiements
        </h1>

        <div>

            <a href="/projet/views/administrator/parameter.php"
               class="btn text-white">
                <i class="bi bi-arrow-left me-2"></i>
                Retour
            </a>

        </div>

    </div>
    <div class=" mt-3">
        <h1 class="text-center text-danger"><i class="fa-solid fa-triangle-exclamation me-2 text-danger fa-beat"></i>Page en travaux</h1>
    </div>

</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<?php

$content = ob_get_clean();

require __DIR__ . '/../../../layout.php';