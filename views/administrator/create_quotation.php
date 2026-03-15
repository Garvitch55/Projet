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

$pdo = getPDO();

// Récupérer tous les clients pour le select
$clients = $pdo->query("SELECT id_client, firstname, lastname FROM gestion_client ORDER BY firstname")->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/../../head.php';
$title = "Créer un devis";

ob_start();
?>

<section class="m-4">
    <h1 class="text-orange-fonce mb-4">Créer un nouveau devis</h1>

    <form action="../../projet/controller/administrator/create_quotation_ctrl.php" method="POST">
        <div class="mb-3">
            <label for="client_id" class="form-label">Client</label>
            <select name="client_id" id="client_id" class="form-select" required>
                <option value="">-- Sélectionner un client --</option>
                <?php foreach ($clients as $client): ?>
                    <option value="<?= $client['id_client'] ?>">
                        <?= htmlentities($client['firstname'].' '.$client['lastname']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="quote_number" class="form-label">Numéro du devis</label>
            <input type="text" name="quote_number" id="quote_number" class="form-control" placeholder="Ex: Q-0001" required>
        </div>

        <div class="mb-3">
            <label for="quote_date" class="form-label">Date du devis</label>
            <input type="date" name="quote_date" id="quote_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Statut</label>
            <select name="status" id="status" class="form-select" required>
                <?php
                $statuses = ['en attente', 'signé', 'annulé'];
                foreach ($statuses as $s):
                ?>
                    <option value="<?= $s ?>"><?= ucfirst($s) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="total_ht" class="form-label">Total HT</label>
            <input type="number" step="0.01" name="total_ht" id="total_ht" class="form-control" value="0" required>
        </div>

        <div class="mb-3">
            <label for="total_vat" class="form-label">TVA</label>
            <input type="number" step="0.01" name="total_vat" id="total_vat" class="form-control" value="0" required>
        </div>

        <div class="mb-3">
            <label for="total_ttc" class="form-label">Total TTC</label>
            <input type="number" step="0.01" name="total_ttc" id="total_ttc" class="form-control" value="0" required>
        </div>

        <button type="submit" class="btn text-white me-2">Créer le devis</button>
        <a href="list_quotation.php" class="btn text-white">Annuler</a>
    </form>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layout.php';