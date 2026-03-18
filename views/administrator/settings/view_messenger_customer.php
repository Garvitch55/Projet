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

if ($id) {
    $pdo = getPDO();

    // On récupère le message depuis la table gestion_client
    $stmt = $pdo->prepare("SELECT * FROM gestion_client WHERE id_client = ?");
    $stmt->execute([$id]);
    $message = $stmt->fetch(PDO::FETCH_ASSOC);

    // Marquer le message comme lu
    if ($message && $message['is_read'] == 0) {
        $update = $pdo->prepare("UPDATE gestion_client SET is_read = 1 WHERE id_client = ?");
        $update->execute([$id]);
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

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-gris-fonce text-white">
                <strong>Demande :</strong> <?= htmlentities($message['demande']) ?>
            </div>
            <div class="card-body">
                <p><strong>Nom :</strong> <?= htmlentities($message['firstname'] . ' ' . $message['lastname']) ?></p>
                <?php if (!empty($message['email'])): ?>
                    <p><strong>Email :</strong> <?= htmlentities($message['email']) ?></p>
                <?php endif; ?>
                <?php if (!empty($message['phone'])): ?>
                    <p><strong>Téléphone :</strong> <?= htmlentities($message['phone']) ?></p>
                <?php endif; ?>
                <p><strong>Message :</strong><br><?= nl2br(htmlentities($message['demande'])) ?></p>
                <p><small>Envoyé le <?= htmlentities($message['created_at']) ?></small></p>
            </div>
        </div>
    <?php else: ?>
        <p>Aucun message trouvé.</p>
        <a href="messenger_customer.php" class="btn text-white mt-3">Retour aux messages</a>
    <?php endif; ?>

</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<?php
$content = ob_get_clean();

// ----------------- Inclusion du layout -----------------
require __DIR__ . '/../../../layout.php';
?>