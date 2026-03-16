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

// -------------------------
// Récupérer l'ID de la facture
// -------------------------
$id_invoice = $_GET['id'] ?? null;
if (!$id_invoice) {
    header("Location: invoice.php?status=danger&message=Facture introuvable");
    exit;
}

$pdo = getPDO();

// -------------------------
// Récupérer la facture et le client
// -------------------------
$stmt = $pdo->prepare("
    SELECT i.*, c.firstname, c.lastname
    FROM invoices i
    JOIN gestion_client c ON i.client_id = c.id_client
    WHERE i.id_invoice = ?
");
$stmt->execute([$id_invoice]);
$invoice = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$invoice) {
    header("Location: invoice.php?status=danger&message=Facture introuvable");
    exit;
}

// -------------------------
// Récupérer les lignes de facture
// -------------------------
$items = $pdo->prepare("SELECT * FROM invoice_items WHERE invoice_id = ?");
$items->execute([$id_invoice]);
$invoice_items = $items->fetchAll(PDO::FETCH_ASSOC);

// -------------------------
// Récupérer travaux, clients et TVA
// -------------------------
$works = $pdo->query("SELECT id_work, name, unit_price FROM works")->fetchAll(PDO::FETCH_ASSOC);
$clients = $pdo->query("SELECT id_client, firstname, lastname FROM gestion_client ORDER BY firstname")->fetchAll(PDO::FETCH_ASSOC);
$tvas = $pdo->query("SELECT id_tva, name, rate FROM tva ORDER BY rate DESC")->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/../../head.php';
$title = "Modifier la facture " . htmlentities($invoice['invoice_number']);

ob_start();
?>

<section class="m-4">
    <div class="d-flex justify-content-between align-items-center mt-3">
        <h1 class="text-orange-fonce mb-4">Modifier la facture <?= htmlentities($invoice['invoice_number']) ?></h1>
        <a href="/projet/views/administrator/invoice.php" class="btn text-white">
            <i class="bi bi-arrow-left me-2"></i> Retour
        </a>
    </div>

    <form action="../projet/controller/administrator/update_invoice_ctrl.php" method="POST">
        <input type="hidden" name="id_invoice" value="<?= $invoice['id_invoice'] ?>">

        <!-- Client -->
        <div class="mb-3">
            <label for="client_id" class="form-label">Client: <span class="text-danger">*</span></label>
            <select name="client_id" id="client_id" class="form-select" required>
                <option value="">-- Sélectionner un client --</option>
                <?php foreach ($clients as $client): ?>
                    <option value="<?= $client['id_client'] ?>" <?= ($invoice['client_id'] == $client['id_client']) ? 'selected' : '' ?>>
                        <?= htmlentities($client['firstname'].' '.$client['lastname']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Numéro facture -->
        <div class="mb-3">
            <label for="invoice_number" class="form-label">Numéro de facture: <span class="text-danger">*</span></label>
            <input type="text" name="invoice_number" id="invoice_number" class="form-control" 
                   value="<?= htmlentities($invoice['invoice_number']) ?>" required>
        </div>

        <!-- Date facture -->
        <div class="mb-3">
            <label for="invoice_date" class="form-label">Date de facture: <span class="text-danger">*</span></label>
            <input type="date" name="invoice_date" id="invoice_date" class="form-control" 
                   value="<?= htmlentities($invoice['invoice_date']) ?>" required>
        </div>

        <!-- Date d’échéance -->
        <div class="mb-3">
            <label for="due_date" class="form-label">Date d'échéance: <span class="text-danger">*</span></label>
            <input type="date" name="due_date" id="due_date" class="form-control" 
                   value="<?= htmlentities($invoice['due_date']) ?>" required>
        </div>

        <!-- Statut -->
        <div class="mb-3">
            <label for="status" class="form-label">Statut: <span class="text-danger">*</span></label>
            <select name="status" id="status" class="form-select" required>
                <?php $statuses = ['brouillon', 'envoyée', 'payée', 'annulée'];
                foreach ($statuses as $s): ?>
                    <option value="<?= $s ?>" <?= ($invoice['status'] === $s) ? 'selected' : '' ?>>
                        <?= ucfirst($s) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- TVA -->
        <div class="mb-3">
            <label for="tva_select" class="form-label">TVA: <span class="text-danger">*</span></label>
            <select id="tva_select" class="form-select" required>
                <?php foreach($tvas as $tva): ?>
                    <option value="<?= $tva['rate'] ?>" <?= ($invoice['total_vat'] == $tva['rate']) ? 'selected' : '' ?>>
                        <?= htmlentities($tva['name']) ?> (<?= $tva['rate'] ?> %)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Lignes de facture -->
        <div id="invoice-items-container">
            <h5>Lignes d'ouvrages</h5>
            <?php foreach($invoice_items as $item): ?>
                <div class="invoice-item row mb-2">
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

        <!-- Totaux -->
        <div class="mb-3">
            <label for="total_ht" class="form-label">Total HT</label>
            <input type="number" step="0.01" id="total_ht" class="form-control" value="<?= $invoice['total_ht'] ?>" disabled>
        </div>
        <div class="mb-3">
            <label for="total_vat" class="form-label">TVA (€)</label>
            <input type="number" step="0.01" id="total_vat" class="form-control" value="<?= $invoice['total_vat'] ?>" disabled>
        </div>
        <div class="mb-3">
            <label for="total_ttc" class="form-label">Total TTC</label>
            <input type="number" step="0.01" id="total_ttc" class="form-control" value="<?= $invoice['total_ttc'] ?>" disabled>
        </div>

        <!-- Champs cachés -->
        <input type="hidden" name="total_ht" id="total_ht_hidden" value="<?= $invoice['total_ht'] ?>">
        <input type="hidden" name="total_vat" id="total_vat_hidden" value="<?= $invoice['total_vat'] ?>">
        <input type="hidden" name="total_ttc" id="total_ttc_hidden" value="<?= $invoice['total_ttc'] ?>">

        <button type="submit" class="btn text-white">Mettre à jour la facture</button>
        <a href="invoice.php" class="btn ms-2 text-white">Annuler</a>
    </form>
</section>

<!-- Template pour ajouter des lignes -->
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
    const tvaSelect = document.getElementById('tva_select');

    const totalHTField = document.getElementById('total_ht');
    const totalVATField = document.getElementById('total_vat');
    const totalTTCField = document.getElementById('total_ttc');

    const totalHTHidden = document.getElementById('total_ht_hidden');
    const totalVATHidden = document.getElementById('total_vat_hidden');
    const totalTTCHidden = document.getElementById('total_ttc_hidden');

    function updateTotals() {
        let totalHT = 0;
        container.querySelectorAll('.invoice-item').forEach(item => {
            if(item.classList.contains('d-none')) return;
            const select = item.querySelector('.work-select');
            const qty = parseInt(item.querySelector('.quantity').value) || 0;
            const price = parseFloat(select.selectedOptions[0]?.dataset.price || 0);
            const lineTotal = price * qty;
            item.querySelector('.total-price').value = lineTotal.toFixed(2);
            totalHT += lineTotal;
        });

        const tvaRate = parseFloat(tvaSelect.value || 0)/100;
        const totalVAT = totalHT * tvaRate;
        const totalTTC = totalHT + totalVAT;

        totalHTField.value = totalHT.toFixed(2);
        totalVATField.value = totalVAT.toFixed(2);
        totalTTCField.value = totalTTC.toFixed(2);

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
    container.querySelectorAll('.invoice-item').forEach(attachEvents);

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