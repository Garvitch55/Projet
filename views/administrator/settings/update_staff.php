<?php
require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../../head.php';

// -------------------------
// Sécurité
// -------------------------
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['role'] !== 'administrateur') {
    header("Location: ../../../index.php");
    exit;
}

$pdo = getPDO();
$title = "Modifier personnel";

// -------------------------
// Récupération ID
// -------------------------
$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: list_staff.php");
    exit;
}

// -------------------------
// Récupération user
// -------------------------
$stmt = $pdo->prepare("SELECT * FROM gestion_personnel WHERE id_personnel = ?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: list_staff.php");
    exit;
}

// -------------------------
ob_start();
?>

<section class="m-4">

    <div class="d-flex justify-content-between align-items-center mt-3">
        <h1 class="text-orange-fonce mb-4">Modifier</h1>
        <a href="views/administrator/settings/list_staff.php" class="btn text-white">
            <i class="bi bi-arrow-left me-2"></i> Retour
        </a>
    </div>

    <form method="POST" action="controller/administrator/update_staff_ctrl.php">

        <input type="hidden" name="id_personnel" value="<?= $user['id_personnel'] ?>">

        <!-- Prénom -->
        <div class="mb-3">
            <label class="form-label">Prénom: <span class="text-danger">*</span></label>
            <input type="text" name="firstname" class="form-control"
                   value="<?= htmlentities($user['firstname']) ?>" required>
        </div>

        <!-- Nom -->
        <div class="mb-3">
            <label class="form-label">Nom: <span class="text-danger">*</span></label>
            <input type="text" name="lastname" class="form-control"
                   value="<?= htmlentities($user['lastname']) ?>" required>
        </div>

        <!-- Email -->
        <div class="mb-3">
            <label class="form-label">Email: <span class="text-danger">*</span></label>
            <input type="email" name="mail" class="form-control"
                   value="<?= htmlentities($user['mail']) ?>" required>
        </div>

        <!-- Téléphone -->
        <div class="mb-3">
            <label class="form-label">Téléphone:</label>
            <input type="text" name="phone" class="form-control"
                   value="<?= htmlentities($user['phone']) ?>">
        </div>

        <!-- ROLE SELECT -->
        <div class="mb-3">
            <label class="form-label">Rôle: <span class="text-danger">*</span></label>
            <select name="fonction" class="form-select" required>
                <option value="employe" <?= $user['fonction'] === 'employe' ? 'selected' : '' ?>>
                    Employé
                </option>
                <option value="administrateur" <?= $user['fonction'] === 'administrateur' ? 'selected' : '' ?>>
                    Administrateur
                </option>
            </select>
        </div>

        <button type="submit" class="btn text-white">
            Mettre à jour l'utilisateur
        </button>

    </form>

</section>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../../layout.php';
?>