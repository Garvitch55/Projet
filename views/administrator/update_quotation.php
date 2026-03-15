<?php
require_once __DIR__ . '/../../config.php';

// -------------------------
// Vérification de connexion
// -------------------------
if (!isset($_SESSION['id'])) {
    header("Location: login.php?status=danger&message=Veuillez vous connecter.");
    exit;
}

// Vérification du rôle administrateur
if ($_SESSION['role'] !== 'administrateur') {
    header("Location: ../../index.php?status=danger&message=Accès refusé.");
    exit;
}

// -------------------------
// Récupérer l'ID du devis
// -------------------------
$id_quote = $_GET['id'] ?? null;
if (!$id_quote) {
    header("Location: list_quotation.php?status=danger&message=Devis introuvable");
    exit;
}

$pdo = getPDO();

// Récupérer les infos du devis + client
$stmt = $pdo->prepare("
    SELECT q.*, c.firstname, c.lastname
    FROM quotes q
    JOIN gestion_client c ON q.client_id = c.id_client
    WHERE q.id_quote = ?
");
$stmt->execute([$id_quote]);
$quote = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$quote) {
    header("Location: list_quotation.php?status=danger&message=Devis introuvable");
    exit;
}

require_once __DIR__ . '/../../head.php';
$title = "Modifier le devis " . htmlentities($quote['quote_number']);

ob_start();
?>

<section class="m-4">
    <h1 class="text-orange-fonce mb-4">Modifier le devis <?= htmlentities($quote['quote_number']) ?></h1>

    <form action="../projet/controller/administrator/update_quotation_ctrl.php" method="POST">
        <input type="hidden" name="id_quote" value="<?= $quote['id_quote'] ?>">

        <div class="mb-3">
            <label for="quote_number" class="form-label">Numéro du devis</label>
            <input type="text" name="quote_number" id="quote_number" class="form-control" 
                   value="<?= htmlentities($quote['quote_number']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="quote_date" class="form-label">Date du devis</label>
            <input type="date" name="quote_date" id="quote_date" class="form-control" 
                   value="<?= htmlentities($quote['quote_date']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Statut</label>
            <select name="status" id="status" class="form-select" required>
                <?php 
                $statuses = ['en attente', 'signé', 'annulé'];
                foreach ($statuses as $s): ?>
                    <option value="<?= $s ?>" <?= ($quote['status'] === $s) ? 'selected' : '' ?>>
                        <?= ucfirst($s) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="total_ht" class="form-label">Total HT</label>
            <input type="number" step="0.01" name="total_ht" id="total_ht" class="form-control"
                   value="<?= $quote['total_ht'] ?>" required>
        </div>

        <div class="mb-3">
            <label for="total_vat" class="form-label">TVA</label>
            <input type="number" step="0.01" name="total_vat" id="total_vat" class="form-control"
                   value="<?= $quote['total_vat'] ?>" required>
        </div>

        <div class="mb-3">
            <label for="total_ttc" class="form-label">Total TTC</label>
            <input type="number" step="0.01" name="total_ttc" id="total_ttc" class="form-control"
                   value="<?= $quote['total_ttc'] ?>" required>
        </div>

        <button type="submit" class="btn text-white">Mettre à jour le devis</button>
        <a href="views/administrator/quotation.php" class="btn ms-2 text-white">Annuler</a>
    </form>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layout.php';