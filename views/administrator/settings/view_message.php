<?php
// views/administrator/settings/view_message.php

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

// ----------------- Inclure le controller -----------------
require __DIR__ . '/../../../controller/administrator/message_ctrl.php';

// ----------------- Récupération de l'id -----------------
$id = $_GET['id'] ?? null;
$message = null;

if ($id) {
    foreach ($all_messages as $msg) {
        if ($msg['id_contact'] == $id) {
            $message = $msg;
            break;
        }
    }
}

// ----------------- Construction du contenu -----------------
ob_start();
?>

<section class="mt-5 mb-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-orange-fonce">Message de <?= htmlentities($message['first_name'] . ' ' . $message['last_name'] ?? '') ?></h1>
        <a href="views/administrator/settings/messages.php" class="btn text-white">
            <i class="bi bi-arrow-left me-2"></i> Retour
        </a>
    </div>

    <?php if ($message): ?>
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-gris-fonce text-white">
                <strong>Sujet :</strong> <?= htmlentities($message['subject']) ?>
            </div>
            <div class="card-body">
                <p><strong>Nom :</strong> <?= htmlentities($message['first_name'] . ' ' . $message['last_name']) ?></p>
                <p><strong>Email :</strong> <?= htmlentities($message['email']) ?></p>
                <p><strong>Téléphone :</strong> <?= htmlentities($message['phone']) ?></p>
                <p><strong>Message :</strong><br><?= nl2br(htmlentities($message['message'])) ?></p>
                <p><small>Envoyé le <?= htmlentities($message['created_at']) ?></small></p>
            </div>
        </div>
    <?php else: ?>
        <p>Aucun message trouvé.</p>
    <?php endif; ?>

</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<?php
$content = ob_get_clean();

// ----------------- Inclusion du layout -----------------
require ROOT . 'layout.php';