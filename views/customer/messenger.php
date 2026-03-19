<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../head.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php?status=danger&message=" . urlencode("Veuillez vous connecter."));
    exit;
}

if ($_SESSION['role'] !== 'client') {
    header("Location: ../../index.php?status=danger&message=" . urlencode("Accès refusé."));
    exit;
}

$title = "Nos échanges";

require __DIR__ . '/../../controller/customer/messenger_ctrl.php';

if (!isset($all_messages)) $all_messages = [];

ob_start();
?>

<section class="m-4">

    <h1 class="text-orange-fonce mb-4">Nos échanges</h1>

    <?php if (!empty($all_messages)): ?>
        <ul class="list-group" id="messagesContainer">

            <?php foreach ($all_messages as $msg): 
                $badge_class = !empty($msg['is_read']) ? 'bg-success' : 'bg-danger';
                $badge_text = !empty($msg['is_read']) ? 'Lu' : 'Non lu';
            ?>

            <li class="list-group-item p-3 position-relative">

                <p class="fw-bold m-0">
                    <?= htmlentities($msg['demande']) ?>
                </p>

                <small class="text-muted">
                    Envoyé le <?= htmlentities($msg['created_at']) ?>
                </small>

                <div class="position-absolute top-0 end-0 m-2">
                    <span class="badge <?= $badge_class ?> rounded-circle d-flex justify-content-center align-items-center"
                          style="width:40px;height:40px;font-size:0.6rem;">
                        <?= ($badge_text === 'Non lu') ? "Non<br>lu" : $badge_text ?>
                    </span>
                </div>

            </li>

            <?php endforeach; ?>

        </ul>
    <?php else: ?>
        <p>Aucune demande trouvée.</p>
    <?php endif; ?>

    <!-- FORMULAIRE -->
    <div class="card mt-4 shadow-sm">
        <div class="card-header bg-orange-fonce text-white">
            <strong>Nouvelle demande</strong>
        </div>
        <div class="card-body">
            <form id="addMessageForm">
                <textarea name="demande" class="form-control mb-3" rows="3" required></textarea>
                <button class="btn text-white">Envoyer</button>
            </form>
        </div>
    </div>
<div class=" mt-3">
    <h1 class="text-center text-danger"><i class="fa-solid fa-triangle-exclamation me-2 text-danger fa-beat"></i>Page en construction</h1>
</div>
</section>

<script>
document.getElementById('addMessageForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch('/projet/controller/customer/add_message_ctrl.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {

        if (data.success) {

            location.reload(); // simple et fiable (car 1 demande stockée)

        } else {
            alert(data.error);
        }

    })
    .catch(() => alert("Erreur serveur"));
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layout.php';
?>