<?php
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

$pdo = getPDO();

// -------------------------
// Récupération données pour le formulaire
// -------------------------
$clients = $pdo->query("SELECT id_client, firstname, lastname FROM gestion_client ORDER BY firstname")->fetchAll(PDO::FETCH_ASSOC);
$works   = $pdo->query("SELECT id_work, name, unit_price FROM works")->fetchAll(PDO::FETCH_ASSOC);
$tvas    = $pdo->query("SELECT id_tva, name, rate FROM tva ORDER BY rate DESC")->fetchAll(PDO::FETCH_ASSOC);

// Seuls les devis signés peuvent être utilisés
$quotes  = $pdo->query("SELECT id_quote, quote_number, client_id FROM quotes WHERE status='signé' ORDER BY quote_date DESC")->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/../../head.php';
$title = "Créer une facture";
ob_start();
?>

<section class="m-4">
    <div class="d-flex justify-content-between align-items-center mt-3">
        <h1 class="text-orange-fonce mb-4">Créer une nouvelle facture</h1>
        <a href="/projet/views/administrator/invoice.php" class="btn text-white">
            <i class="bi bi-arrow-left me-2"></i> Retour
        </a>
    </div>

    <form action="../../projet/controller/administrator/create_invoice_ctrl.php" method="POST">
        <!-- Sélection du devis -->
        <div class="mb-3">
            <label for="quote_id" class="form-label">Devis (optionnel)</label>
            <select name="quote_id" id="quote_id" class="form-select">
                <option value="">-- Aucun devis --</option>
                <?php foreach($quotes as $quote): ?>
                    <option value="<?= $quote['id_quote'] ?>" data-client="<?= $quote['client_id'] ?>">
                        <?= htmlentities($quote['quote_number']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Client -->
        <div class="mb-3">
            <label for="client_id" class="form-label">Client: <span class="text-danger">*</span></label>
            <select name="client_id" id="client_id" class="form-select" required>
                <option value="">-- Sélectionner un client --</option>
                <?php foreach ($clients as $client): ?>
                    <option value="<?= $client['id_client'] ?>">
                        <?= htmlentities($client['firstname'].' '.$client['lastname']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Numéro facture -->
        <div class="mb-3">
            <label for="invoice_number" class="form-label">Numéro de facture:  <span class="text-danger">*</span></label>
            <input type="text" name="invoice_number" id="invoice_number" class="form-control" placeholder="Ex: F-0001" required>
        </div>

        <!-- Date facture -->
        <div class="mb-3">
            <label for="invoice_date" class="form-label">Date de facture:  <span class="text-danger">*</span></label>
            <input type="date" name="invoice_date" id="invoice_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
        </div>

        <!-- Date d’échéance -->
        <div class="mb-3">
            <label for="due_date" class="form-label">Date d'échéance:  <span class="text-danger">*</span></label>
            <input type="date" name="due_date" id="due_date" class="form-control" required>
        </div>

        <!-- Statut -->
        <div class="mb-3">
            <label for="status" class="form-label">Statut:  <span class="text-danger">*</span></label>
            <select name="status" id="status" class="form-select" required>
                <?php $statuses = ['brouillon', 'envoyée', 'payée', 'annulée'];
                foreach ($statuses as $s): ?>
                    <option value="<?= $s ?>"><?= ucfirst($s) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- TVA -->
        <div class="mb-3">
            <label for="tva_id" class="form-label">TVA:  <span class="text-danger">*</span></label>
            <select name="tva_id" id="tva_id" class="form-select" required>
                <option value="">-- Sélectionner un taux de TVA --</option>
                <?php foreach($tvas as $tva): ?>
                    <option value="<?= $tva['id_tva'] ?>" data-rate="<?= $tva['rate'] ?>">
                        <?= htmlentities($tva['name'].' ('.$tva['rate'].'%)') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Lignes facture -->
        <div id="invoice-items-container">
            <h5>Lignes d'ouvrages</h5>
        </div>
        <button type="button" id="add-item-btn" class="btn text-white mb-3">Ajouter un ouvrage</button>

        <!-- Totaux -->
        <div class="mb-3">
            <label for="total_ht" class="form-label">Total HT</label>
            <input type="number" step="0.01" id="total_ht" class="form-control" value="0" disabled>
        </div>
        <div class="mb-3">
            <label for="total_vat" class="form-label">Total TVA (€)</label>
            <input type="number" step="0.01" id="total_vat" class="form-control" value="0" disabled>
        </div>
        <div class="mb-3">
            <label for="total_ttc" class="form-label">Total TTC</label>
            <input type="number" step="0.01" id="total_ttc" class="form-control" value="0" disabled>
        </div>

        <!-- Champs cachés -->
        <input type="hidden" name="total_ht" id="total_ht_hidden" value="0">
        <input type="hidden" name="total_vat" id="total_vat_hidden" value="0">
        <input type="hidden" name="total_ttc" id="total_ttc_hidden" value="0">

        <button type="submit" class="btn text-white me-2">Créer la facture</button>
        <a href="invoice.php" class="btn text-white">Annuler</a>
    </form>
</section>

<!-- Template ligne facture -->
<div class="invoice-item row mb-2 d-none" id="invoice-item-template">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('invoice-items-container');
    const template = document.getElementById('invoice-item-template');
    const addBtn = document.getElementById('add-item-btn');
    const tvaSelect = document.getElementById('tva_id');
    const quoteSelect = document.getElementById('quote_id');
    const clientSelect = document.getElementById('client_id');

    const totalHT = document.getElementById('total_ht');
    const totalVAT = document.getElementById('total_vat');
    const totalTTC = document.getElementById('total_ttc');
    const totalHTHidden = document.getElementById('total_ht_hidden');
    const totalVATHidden = document.getElementById('total_vat_hidden');
    const totalTTCHidden = document.getElementById('total_ttc_hidden');

    function updateTotals() {
        let sumHT = 0;
        container.querySelectorAll('.invoice-item').forEach(item => {
            if(item.classList.contains('d-none')) return;
            const select = item.querySelector('.work-select');
            const qty = parseInt(item.querySelector('.quantity').value) || 0;
            const price = parseFloat(select.selectedOptions[0]?.dataset.price || 0);
            const lineTotal = price * qty;
            item.querySelector('.total-price').value = lineTotal.toFixed(2);
            sumHT += lineTotal;
        });

        const tvaRate = parseFloat(tvaSelect.selectedOptions[0]?.dataset.rate || 0);
        const vatAmount = sumHT * tvaRate / 100;
        const ttc = sumHT + vatAmount;

        totalHT.value = sumHT.toFixed(2);
        totalVAT.value = vatAmount.toFixed(2);
        totalTTC.value = ttc.toFixed(2);
        totalHTHidden.value = sumHT.toFixed(2);
        totalVATHidden.value = vatAmount.toFixed(2);
        totalTTCHidden.value = ttc.toFixed(2);
    }

    function attachEvents(line) {
        line.querySelector('.work-select').addEventListener('change', updateTotals);
        line.querySelector('.quantity').addEventListener('input', updateTotals);
        line.querySelector('.remove-item').addEventListener('click', () => {
            line.remove();
            updateTotals();
        });
    }

    addBtn.addEventListener('click', () => {
        const clone = template.cloneNode(true);
        clone.classList.remove('d-none');
        clone.id = '';
        container.appendChild(clone);
        attachEvents(clone);
        updateTotals();
    });

    tvaSelect.addEventListener('change', updateTotals);

    // Charger les lignes depuis un devis sélectionné via AJAX
    quoteSelect.addEventListener('change', function() {
        const quoteId = this.value;
        container.innerHTML = ''; // vider les lignes existantes
        if(!quoteId) return;

        const clientId = this.selectedOptions[0].dataset.client;
        clientSelect.value = clientId;

        fetch(`../../projet/controller/administrator/ajax_get_quote_lines.php?id_quote=${quoteId}`)
            .then(resp => resp.json())
            .then(data => {
                data.forEach(line => {
                    const clone = template.cloneNode(true);
                    clone.classList.remove('d-none');
                    clone.id = '';
                    container.appendChild(clone);

                    const select = clone.querySelector('.work-select');
                    select.value = line.work_id;
                    clone.querySelector('.quantity').value = line.quantity;
                    attachEvents(clone);
                });
                updateTotals();
            });
    });

    // Ajouter première ligne par défaut
    addBtn.click();
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layout.php';
?>