<?php
require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../../head.php';

// ----------------- Vérification de connexion -----------------
if (!isset($_SESSION['id'])) {
    header("Location: ../../login.php?status=danger&message=" . urlencode("Veuillez vous connecter."));
    exit;
}

// ----------------- Vérification du rôle -----------------
if ($_SESSION['role'] !== 'administrateur') {
    header("Location: ../../../index.php?status=danger&message=" . urlencode("Accès refusé."));
    exit;
}

// ----------------- Titre -----------------
$title = "Voir le message";

// ----------------- Récupération de l'id -----------------
$id = $_GET['id'] ?? null;
$message = null;
$replies = [];

if ($id) {
    $pdo = getPDO();

    // Récupérer le message depuis gestion_client
    $stmt = $pdo->prepare("SELECT * FROM gestion_client WHERE id_client = ?");
    $stmt->execute([$id]);
    $message = $stmt->fetch(PDO::FETCH_ASSOC);

    // Marquer le message comme lu
    if ($message && $message['is_read'] == 0) {
        $update = $pdo->prepare("UPDATE gestion_client SET is_read = 1 WHERE id_client = ?");
        $update->execute([$id]);
    }

    // Récupérer les réponses pour ce client (plus récentes en premier)
    if ($message) {
        $stmtReplies = $pdo->prepare("
            SELECT r.*, 
                   COALESCE(p.firstname, ?) AS personnel_firstname, 
                   COALESCE(p.lastname, ?) AS personnel_lastname
            FROM client_replies r
            LEFT JOIN gestion_personnel p ON r.personnel_id = p.id_personnel
            WHERE r.client_id = ?
            ORDER BY r.created_at DESC
        ");
        $stmtReplies->execute([$_SESSION['name'], '', $message['id_client']]); // fallback sur $_SESSION['name']
        $replies = $stmtReplies->fetchAll(PDO::FETCH_ASSOC);
    }
}

// ----------------- Construction du contenu -----------------
ob_start();
?>

<section class="mt-5 mb-5">
    <?php if ($message): ?>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="text-orange-fonce">Message de <?= htmlentities($message['firstname'] . ' ' . $message['lastname']) ?></h1>
            <a href="views/administrator/settings/messenger_customer.php" class="btn text-white">
                <i class="bi bi-arrow-left me-2"></i> Retour
            </a>
        </div>

        <!-- Message du client -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-gris-fonce text-white">
                <h4 class="pt-2"><b>Demande :</b> <?= htmlentities($message['demande']) ?></h4>
            </div>
            <div class="card-body">
                <p><strong>Nom :</strong> <?= htmlentities($message['firstname'] . ' ' . $message['lastname']) ?></p>
                <?php if (!empty($message['email'])): ?>
                    <p><b>Email :</b> <?= htmlentities($message['email']) ?></p>
                <?php endif; ?>
                <?php if (!empty($message['phone'])): ?>
                    <p><b>Téléphone: </b> <?= htmlentities($message['phone']) ?></p>
                <?php endif; ?>
                <p><b>Message: </b><?= nl2br(htmlentities($message['demande'])) ?></p>
                <p><b>Reçu le :</b> <?= date('d/m/Y H:i', strtotime($message['created_at'])) ?></p>
            </div>
        </div>

        <!-- Toutes les réponses -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Réponses précédentes</h5>
            </div>
            <div id="repliesContainer" class="card-body" style="max-height: 300px; overflow-y:auto;">
               <?php foreach ($replies as $reply): ?>
        <?php if (!empty($reply['reply_personnel'])): ?>
            <!-- Réponse du personnel à droite -->
            <div class="d-flex justify-content-end">
                <div class="p-2 border rounded bg-orange-fonce text-white" style="max-width:70%;">
                    <div><?= date('d/m/Y H:i', strtotime($reply['created_at'])) ?> - Répondu par <?= htmlentities($reply['personnel_firstname'] . ' ' . $reply['personnel_lastname']) ?></div>
                    <div class="mb-0"><?= nl2br(htmlentities($reply['reply_personnel'])) ?></div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($reply['reply_client'])): ?>
            <!-- Réponse du client à gauche -->
            <div class="d-flex justify-content-start">
                <div class="p-2 border rounded text-white bg-gris-fonce " style="max-width:70%;">
                    <div><?= date('d/m/Y H:i', strtotime($reply['created_at'])) ?> - <?= htmlentities($message['firstname'] . ' ' . $message['lastname']) ?></div>
                    <div class="mb-0"><?= nl2br(htmlentities($reply['reply_client'])) ?></div>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
            </div>
        </div>

        <!-- Formulaire de réponse AJAX -->
        <div class="card shadow-sm mb-5">
            <div class="card-header bg-orange-fonce text-white">
                <h5 class="mb-0">Répondre au client</h5>
            </div>
            <div class="card-body">
                <form id="replyForm" method="post">
                    <input type="hidden" name="id_client" value="<?= (int)$message['id_client'] ?>">
                    <div class="mb-3">
                        <label for="reply" class="form-label">Votre réponse :</label>
                        <textarea name="reply" id="reply" rows="5" class="form-control" required></textarea>
                    </div>
                    <button type="submit" class="btn text-white">
                        Envoyer la réponse
                    </button>
                </form>
            </div>
        </div>

    <?php else: ?>
        <p>Aucun message trouvé.</p>
        <a href="views/administrator/settings/messenger_customer.php" class="btn text-white mt-3">Retour aux messages</a>
    <?php endif; ?>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.getElementById('replyForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);

    fetch('controller/administrator/send_reply_ctrl.php', {
        method: 'POST',
        body: formData,
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            // div pour la réponse du personnel à droite
            const replyDiv = document.createElement('div');
            replyDiv.classList.add('d-flex', 'justify-content-end');

            const innerDiv = document.createElement('div');
            innerDiv.classList.add('p-2', 'border', 'rounded', 'bg-orange-fonce', 'text-white');
            innerDiv.style.maxWidth = '70%';

            innerDiv.innerHTML = `
                <div class="text-white p-0">${data.created_at} - Répondu par ${data.personnel_name}</div>
                <div class="mb-0 text-white">${data.message}</div>
            `;

            replyDiv.appendChild(innerDiv);

            // Ajouter la réponse en haut du container
            const container = document.getElementById('repliesContainer');
            container.insertBefore(replyDiv, container.firstChild);

            // Réinitialiser le formulaire
            form.reset();
        } else {
            alert(data.error || 'Erreur lors de l\'envoi de la réponse.');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Erreur lors de l\'envoi de la réponse.');
    });
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../../layout.php';
?>