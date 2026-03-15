<?php
require_once __DIR__ . '/../../config.php';

// Vérification connexion et rôle
if (!isset($_SESSION['id'])) {
    header("Location: login.php?status=danger&message=Veuillez vous connecter.");
    exit;
}
if ($_SESSION['role'] !== 'administrateur') {
    header("Location: ../../index.php?status=danger&message=Accès refusé.");
    exit;
}

// Récupérer l'ID du devis
$id_quote = $_GET['id'] ?? null;
if (!$id_quote) {
    header("Location: list_quotation.php?status=danger&message=Devis introuvable");
    exit;
}

$pdo = getPDO();

// Récupérer le devis et le client
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

// Récupérer les lignes du devis
$items = $pdo->prepare("SELECT * FROM quote_items WHERE quote_id = ?");
$items->execute([$id_quote]);
$quote_items = $items->fetchAll(PDO::FETCH_ASSOC);

// Tous les travaux et TVA
$works = $pdo->query("SELECT id_work, name, unit_price FROM works")->fetchAll(PDO::FETCH_ASSOC);
$tvas = $pdo->query("SELECT id_tva, name, rate FROM tva ORDER BY rate DESC")->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/../../head.php';
$title = "Modifier le devis " . htmlentities($quote['quote_number']);

ob_start();
?>

<section class="m-4">
    <div class="d-flex justify-content-between align-items-center mt-3">
        <h1 class="text-orange-fonce mb-4">Modifier le devis <?= htmlentities($quote['quote_number']) ?></h1>
        <a href="/projet/views/administrator/quotation.php" class="btn text-white">
            <i class="bi bi-arrow-left me-2"></i> Retour
        </a>
    </div>

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

        <!-- Lignes d'ouvrages -->
        <div id="quote-items-container">
            <h5>Lignes d'ouvrages</h5>
            <?php foreach($quote_items as $item): ?>
                <div class="quote-item row mb-2">
                    <div class="col-5">
                        <select name="work_id[]" class="form-select work-select" required>
                            <option value="">-- Sélectionner un ouvrage --</option>
                            <?php foreach($works as $work): ?>
                                <option value="<?= $work['id_work'] ?>" data-price="<?= $work['unit_price'] ?>"
                                    <?= ($item['work_id'] == $work['id_work']) ? 'selected' : '' ?>>
                                    <?= htmlentities($work['name']) ?> - <?= number_format($work['unit_price'],2,',',' ') ?> €
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-2">
                        <input type="number" name="quantity[]" class="form-control quantity" min="1" value="<?= $item['quantity'] ?>" required>
                    </div>
                    <div class="col-3">
                        <input type="text" class="form-control total-price" readonly value="<?= number_format($item['total_price'],2,'.','') ?>">
                    </div>
                    <div class="col-2">
                        <button type="button" class="btn text-white btn-sm remove-item"><i class="fa-solid fa-trash fa-bounce"></i></button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <button type="button" id="add-item-btn" class="btn text-white mb-3">Ajouter un ouvrage</button>

        <!-- TVA -->
        <div class="mb-3">
            <label for="tva_select" class="form-label">TVA</label>
            <select id="tva_select" class="form-select" required>
                <?php foreach($tvas as $tva): ?>
                    <option value="<?= $tva['rate'] ?>" <?= ($quote['total_vat'] == $tva['rate']) ? 'selected' : '' ?>>
                        <?= htmlentities($tva['name']) ?> (<?= $tva['rate'] ?> %)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Totaux désactivés -->
        <div class="mb-3">
            <label for="total_ht" class="form-label">Total HT</label>
            <input type="number" step="0.01" id="total_ht" class="form-control" value="<?= $quote['total_ht'] ?>" disabled>
        </div>
        <div class="mb-3">
            <label for="total_vat" class="form-label">TVA (€)</label>
            <input type="number" step="0.01" id="total_vat" class="form-control" value="<?= $quote['total_vat'] ?>" disabled>
        </div>
        <div class="mb-3">
            <label for="total_ttc" class="form-label">Total TTC</label>
            <input type="number" step="0.01" id="total_ttc" class="form-control" value="<?= $quote['total_ttc'] ?>" disabled>
        </div>

        <!-- Champs cachés pour envoi au serveur -->
        <input type="hidden" name="total_ht" id="total_ht_hidden" value="<?= $quote['total_ht'] ?>">
        <input type="hidden" name="total_vat" id="total_vat_hidden" value="<?= $quote['total_vat'] ?>">
        <input type="hidden" name="total_ttc" id="total_ttc_hidden" value="<?= $quote['total_ttc'] ?>">

        <button type="submit" class="btn text-white">Mettre à jour le devis</button>
        <a href="views/administrator/quotation.php" class="btn ms-2 text-white">Annuler</a>
    </form>
</section>

<!-- Template pour nouvelles lignes -->
<div class="quote-item row mb-2 d-none" id="quote-item-template">
    <div class="col-5">
        <select name="work_id[]" class="form-select work-select" required>
            <option value="">-- Sélectionner un ouvrage --</option>
            <?php foreach($works as $work): ?>
                <option value="<?= $work['id_work'] ?>" data-price="<?= $work['unit_price'] ?>">
                    <?= htmlentities($work['name']) ?> - <?= number_format($work['unit_price'],2,',',' ') ?> €
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-2">
        <input type="number" name="quantity[]" class="form-control quantity" min="1" value="1" required>
    </div>
    <div class="col-3">
        <input type="text" class="form-control total-price" readonly>
    </div>
    <div class="col-2">
        <button type="button" class="btn text-white btn-sm remove-item"><i class="fa-solid fa-trash fa-bounce"></i></button>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('quote-items-container');
    const template = document.getElementById('quote-item-template');
    const addBtn = document.getElementById('add-item-btn');
    const tvaSelect = document.getElementById('tva_select');

    const totalHTField = document.getElementById('total_ht');
    const totalVATField = document.getElementById('total_vat');
    const totalTTCField = document.getElementById('total_ttc');

    const totalHTHidden = document.getElementById('total_ht_hidden');
    const totalVATHidden = document.getElementById('total_vat_hidden');
    const totalTTCHidden = document.getElementById('total_ttc_hidden');

    function updateTotals() {
        let totalHT = 0;
        container.querySelectorAll('.quote-item').forEach(item => {
            if(item.classList.contains('d-none')) return;
            const select = item.querySelector('.work-select');
            const qty = parseInt(item.querySelector('.quantity').value) || 0;
            const price = parseFloat(select.selectedOptions[0]?.dataset.price || 0);
            const lineTotal = price * qty;
            item.querySelector('.total-price').value = lineTotal.toFixed(2);
            totalHT += lineTotal;
        });

        const tvaRate = parseFloat(tvaSelect.value || 0) / 100;
        const totalVAT = totalHT * tvaRate;
        const totalTTC = totalHT + totalVAT;

        // Affichage
        totalHTField.value = totalHT.toFixed(2);
        totalVATField.value = totalVAT.toFixed(2);
        totalTTCField.value = totalTTC.toFixed(2);

        // Champs cachés pour POST
        totalHTHidden.value = totalHT.toFixed(2);
        totalVATHidden.value = totalVAT.toFixed(2);
        totalTTCHidden.value = totalTTC.toFixed(2);
    }

    function attachEvents(line) {
        line.querySelector('.work-select').addEventListener('change', updateTotals);
        line.querySelector('.quantity').addEventListener('input', updateTotals);
        line.querySelector('.remove-item').addEventListener('click', () => {
            line.remove();
            updateTotals();
        });
    }

    // Lignes existantes
    container.querySelectorAll('.quote-item').forEach(attachEvents);

    // Ajouter une nouvelle ligne
    addBtn.addEventListener('click', function() {
        const clone = template.cloneNode(true);
        clone.classList.remove('d-none');
        clone.id = '';
        container.appendChild(clone);
        attachEvents(clone);
        updateTotals();
    });

    // Changement du select TVA
    tvaSelect.addEventListener('change', updateTotals);

    // Calcul initial
    updateTotals();
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layout.php';
?>