<?php
// update_customer.php
require_once __DIR__ . '/../../config.php';

// -------------------------
// Vérification connexion et rôle
// -------------------------
if (!isset($_SESSION['id'])) {
    header("Location: login.php?status=danger&message=Veuillez vous connecter.");
    exit;
}
if ($_SESSION['role'] !== 'administrateur') {
    header("Location: ../../index.php?status=danger&message=Accès refusé.");
    exit;
}

// -------------------------
// Récupérer l'ID du client
// -------------------------
$id_client = $_GET['id'] ?? null;
if (!$id_client) {
    header("Location: customer.php?status=danger&message=Client introuvable");
    exit;
}

$pdo = getPDO();

// -------------------------
// Récupérer les informations du client
// -------------------------
$stmt = $pdo->prepare("SELECT * FROM gestion_client WHERE id_client = ?");
$stmt->execute([$id_client]);
$client = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$client) {
    header("Location: customer.php?status=danger&message=Client introuvable");
    exit;
}

require_once __DIR__ . '/../../head.php';
$title = "Modifier le client " . htmlentities($client['firstname'] . ' ' . $client['lastname']);

ob_start();
?>

<section class="m-4">
    <div class="d-flex justify-content-between align-items-center mt-3">
        <h1 class="text-orange-fonce mb-4">Modifier le client <?= htmlentities($client['firstname'] . ' ' . $client['lastname']) ?></h1>
        <a href="/projet/views/administrator/customer.php" class="btn text-white">
            <i class="bi bi-arrow-left me-2"></i> Retour
        </a>
    </div>

    <form action="/projet/controller/administrator/update_customer_ctrl.php" method="POST">
        <input type="hidden" name="id_client" value="<?= $client['id_client'] ?>">

        <!-- Prénom -->
        <div class="mb-3">
            <label for="firstname" class="form-label">Prénom: <span class="text-danger">*</span></label>
            <input type="text" name="firstname" id="firstname" class="form-control" 
                   value="<?= htmlentities($client['firstname']) ?>" required>
        </div>

        <!-- Nom -->
        <div class="mb-3">
            <label for="lastname" class="form-label">Nom: <span class="text-danger">*</span></label>
            <input type="text" name="lastname" id="lastname" class="form-control" 
                   value="<?= htmlentities($client['lastname']) ?>" required>
        </div>

        <!-- Email -->
        <div class="mb-3">
            <label for="email" class="form-label">Email:</label>
            <input type="email" name="email" id="email" class="form-control" 
                   value="<?= htmlentities($client['email']) ?>">
        </div>

        <!-- Téléphone -->
        <div class="mb-3">
            <label for="phone" class="form-label">Téléphone:</label>
            <input type="text" name="phone" id="phone" class="form-control" 
                   value="<?= htmlentities($client['phone']) ?>">
        </div>

        <!-- Adresse -->
        <div class="mb-3">
            <label for="rue" class="form-label">Rue:</label>
            <input type="text" name="rue" id="rue" class="form-control" 
                   value="<?= htmlentities($client['rue']) ?>">
        </div>

        <div class="mb-3">
            <label for="cp" class="form-label">Code postal:</label>
            <input type="text" name="cp" id="cp" class="form-control" 
                   value="<?= htmlentities($client['cp']) ?>">
        </div>

        <div class="mb-3">
            <label for="ville" class="form-label">Ville:</label>
            <input type="text" name="ville" id="ville" class="form-control" 
                   value="<?= htmlentities($client['ville']) ?>">
        </div>

        <button type="submit" class="btn text-white">Mettre à jour le client</button>
        <a href="customer.php" class="btn ms-2 text-white">Annuler</a>
    </form>
</section>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layout.php';
?>