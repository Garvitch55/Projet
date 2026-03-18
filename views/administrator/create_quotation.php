<?php
require_once __DIR__ . '/../../config.php';

// Vérification de connexion et rôle
if (!isset($_SESSION['id'])) {
    header("Location: login.php?status=danger&message=Veuillez vous connecter.");
    exit;
}
if ($_SESSION['role'] !== 'administrateur') {
    header("Location: ../../index.php?status=danger&message=Accès refusé.");
    exit;
}

$pdo = getPDO();

// Récupérer clients, travaux et TVA
$clients = $pdo->query("SELECT id_client, firstname, lastname FROM gestion_client ORDER BY firstname")->fetchAll(PDO::FETCH_ASSOC);
$works = $pdo->query("SELECT id_work, name, unit_price FROM works")->fetchAll(PDO::FETCH_ASSOC);
$tvas = $pdo->query("SELECT id_tva, name, rate FROM tva ORDER BY rate DESC")->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/../../head.php';
$title = "Créer un devis";
ob_start();
?>

<section class="m-4">
    <div class="d-flex justify-content-between align-items-center mt-3">
        <h1 class="text-orange-fonce mb-4">Créer un nouveau devis</h1>
        <a href="/projet/views/administrator/quotation.php" class="btn text-white">
            <i class="bi bi-arrow-left me-2"></i> Retour
        </a>
    </div>

    <form action="../../projet/controller/administrator/create_quotation_ctrl.php" method="POST">
        <!-- Client -->
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

        <!-- Numéro devis -->
        <div class="mb-3">
            <label for="quote_number" class="form-label">Numéro du devis</label>
            <input type="text" name="quote_number" id="quote_number" class="form-control" placeholder="Ex: Q-0001" required>
        </div>

        <!-- Date devis -->
        <div class="mb-3">
            <label for="quote_date" class="form-label">Date du devis</label>
            <input type="date" name="quote_date" id="quote_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
        </div>

        <!-- Statut -->
        <div class="mb-3">
            <label for="status" class="form-label">Statut</label>
            <select name="status" id="status" class="form-select" required>
                <?php $statuses = ['en attente', 'signé', 'annulé'];
                foreach ($statuses as $s): ?>
                    <option value="<?= $s ?>"><?= ucfirst($s) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Sélecteur TVA -->
        <div class="mb-3">
            <label for="tva_id" class="form-label">TVA</label>
            <select name="tva_id" id="tva_id" class="form-select" required>
                <option value="">-- Sélectionner un taux de TVA --</option>
                <?php foreach($tvas as $tva): ?>
                    <option value="<?= $tva['id_tva'] ?>" data-rate="<?= $tva['rate'] ?>">
                        <?= htmlentities($tva['name'].' ('.$tva['rate'].'%)') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Lignes d'ouvrages -->
        <div id="quote-items-container">
            <h5>Lignes d'ouvrages</h5>
        </div>

        <button type="button" id="add-item-btn" class="btn text-white mb-3">Ajouter un ouvrage</button>

        <!-- Totaux désactivés -->
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

        <!-- Champs cachés pour POST -->
        <input type="hidden" name="total_ht" id="total_ht_hidden" value="0">
        <input type="hidden" name="total_vat" id="total_vat_hidden" value="0">
        <input type="hidden" name="total_ttc" id="total_ttc_hidden" value="0">

        <button type="submit" class="btn text-white me-2">Créer le devis</button>
        <a href="list_quotation.php" class="btn text-white">Annuler</a>
    </form>
</section>

<!-- Template des lignes -->
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('quote-items-container');
    const template = document.getElementById('quote-item-template');
    const addBtn = document.getElementById('add-item-btn');
    const tvaSelect = document.getElementById('tva_id');

    const totalHT = document.getElementById('total_ht');
    const totalVAT = document.getElementById('total_vat');
    const totalTTC = document.getElementById('total_ttc');

    const totalHTHidden = document.getElementById('total_ht_hidden');
    const totalVATHidden = document.getElementById('total_vat_hidden');
    const totalTTCHidden = document.getElementById('total_ttc_hidden');

    function updateTotals() {
        let sumHT = 0;
        container.querySelectorAll('.quote-item').forEach(item => {
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

        // Affichage désactivé
        totalHT.value = sumHT.toFixed(2);
        totalVAT.value = vatAmount.toFixed(2);
        totalTTC.value = ttc.toFixed(2);

        // Champs cachés pour POST
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

    // Ajouter première ligne par défaut
    addBtn.click();
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<?php
$content = ob_get_clean();
require __DIR__ . '/../../layout.php';
?>