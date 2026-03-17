<?php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Faker\Factory;

$pdo = getPDO();
$faker = Factory::create('fr_FR');

echo "Chargement de invoices_fixture.php...\n";

// ---------------------------
// Vider les tables invoices et invoice_items
// ---------------------------
$pdo->exec("DELETE FROM invoice_items");
$pdo->exec("DELETE FROM invoices");

// ---------------------------
// Récupérer tous les clients
// ---------------------------
$clients = $pdo->query("SELECT id_client FROM gestion_client")->fetchAll(PDO::FETCH_ASSOC);
if (empty($clients)) die("Aucun client trouvé. Générer d'abord les clients.\n");

// ---------------------------
// Récupérer uniquement les devis signés
// ---------------------------
$quotes = $pdo->query("SELECT id_quote, client_id FROM quotes WHERE status = 'signé'")->fetchAll(PDO::FETCH_ASSOC);

// ---------------------------
// Récupérer tous les ouvrages
// ---------------------------
$works = $pdo->query("SELECT id_work, name, unit_price FROM works")->fetchAll(PDO::FETCH_ASSOC);
if (empty($works)) die("Aucun ouvrage trouvé. Générer d'abord les ouvrages.\n");

// ---------------------------
// Préparer l'insertion des factures
// ---------------------------
$stmtInvoice = $pdo->prepare("
    INSERT INTO invoices
    (quote_id, client_id, invoice_number, invoice_date, due_date, status, total_ht, total_vat, total_ttc, created_at)
    VALUES (:quote_id, :client_id, :invoice_number, :invoice_date, :due_date, :status, :total_ht, :total_vat, :total_ttc, :created_at)
");

// ---------------------------
// Préparer l'insertion des lignes de factures
// ---------------------------
$stmtItem = $pdo->prepare("
    INSERT INTO invoice_items
    (invoice_id, work_id, description, quantity, unit_price, total_price, created_at)
    VALUES (:invoice_id, :work_id, :description, :quantity, :unit_price, :total_price, :created_at)
");

// ---------------------------
// Statuts en français pour les factures
// ---------------------------
$statuses = ['brouillon', 'envoyée', 'payée', 'annulée'];
$invoiceCounter = 1;

// ---------------------------
// Générer au moins 3 factures par client
// ---------------------------
foreach ($clients as $client) {
    for ($i = 0; $i < 3; $i++) {

        // Choisir un devis signé pour ce client à 70% ou null sinon
        $clientQuotes = array_filter($quotes, fn($q) => $q['client_id'] == $client['id_client']);
        if (!empty($clientQuotes) && $faker->boolean(70)) {
            $quote = $faker->randomElement($clientQuotes);
            $quoteId = $quote['id_quote'];
        } else {
            $quoteId = null;
        }

        $status = $faker->randomElement($statuses);
        $invoiceDate = $faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d');
        $dueDate = date('Y-m-d', strtotime($invoiceDate . ' +30 days'));

        // Insérer la facture
        $stmtInvoice->execute([
            'quote_id'       => $quoteId,
            'client_id'      => $client['id_client'],
            'invoice_number' => 'FAC-2025-' . str_pad($invoiceCounter, 4, '0', STR_PAD_LEFT),
            'invoice_date'   => $invoiceDate,
            'due_date'       => $dueDate,
            'status'         => $status,
            'total_ht'       => 0,
            'total_vat'      => 0,
            'total_ttc'      => 0,
            'created_at'     => date('Y-m-d H:i:s')
        ]);

        $invoiceId = $pdo->lastInsertId();
        $totalHT = 0;

        // 1 à 5 lignes de facture
        $nbLines = $faker->numberBetween(1, 5);
        for ($j = 0; $j < $nbLines; $j++) {
            $work = $faker->randomElement($works);
            $quantity = $faker->numberBetween(1, 10);
            $lineTotal = $work['unit_price'] * $quantity;
            $totalHT += $lineTotal;

            $stmtItem->execute([
                'invoice_id'  => $invoiceId,
                'work_id'     => $work['id_work'],
                'description' => $work['name'],
                'quantity'    => $quantity,
                'unit_price'  => $work['unit_price'],
                'total_price' => $lineTotal,
                'created_at'  => date('Y-m-d H:i:s')
            ]);
        }

        // Totaux
        $totalVAT = 0;
        $totalTTC = $totalHT;
        $pdo->prepare("UPDATE invoices SET total_ht = ?, total_vat = ?, total_ttc = ? WHERE id_invoice = ?")
            ->execute([$totalHT, $totalVAT, $totalTTC, $invoiceId]);

        $invoiceCounter++;
    }
}

// ---------------------------
// Générer des factures aléatoires supplémentaires pour atteindre 90
// ---------------------------
$remaining = 90 - $invoiceCounter + 1;
for ($i = 0; $i < $remaining; $i++) {
    // Choisir un devis signé à 70% ou un client aléatoire sinon
    if (!empty($quotes) && $faker->boolean(70)) {
        $quote = $faker->randomElement($quotes);
        $quoteId = $quote['id_quote'];
        $clientId = $quote['client_id'];
    } else {
        $quoteId = null;
        $client = $faker->randomElement($clients);
        $clientId = $client['id_client'];
    }

    $status = $faker->randomElement($statuses);
    $invoiceDate = $faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d');
    $dueDate = date('Y-m-d', strtotime($invoiceDate . ' +30 days'));

    $stmtInvoice->execute([
        'quote_id'       => $quoteId,
        'client_id'      => $clientId,
        'invoice_number' => 'FAC-2025-' . str_pad($invoiceCounter, 4, '0', STR_PAD_LEFT),
        'invoice_date'   => $invoiceDate,
        'due_date'       => $dueDate,
        'status'         => $status,
        'total_ht'       => 0,
        'total_vat'      => 0,
        'total_ttc'      => 0,
        'created_at'     => date('Y-m-d H:i:s')
    ]);

    $invoiceId = $pdo->lastInsertId();
    $totalHT = 0;

    $nbLines = $faker->numberBetween(1, 5);
    for ($j = 0; $j < $nbLines; $j++) {
        $work = $faker->randomElement($works);
        $quantity = $faker->numberBetween(1, 10);
        $lineTotal = $work['unit_price'] * $quantity;
        $totalHT += $lineTotal;

        $stmtItem->execute([
            'invoice_id'  => $invoiceId,
            'work_id'     => $work['id_work'],
            'description' => $work['name'],
            'quantity'    => $quantity,
            'unit_price'  => $work['unit_price'],
            'total_price' => $lineTotal,
            'created_at'  => date('Y-m-d H:i:s')
        ]);
    }

    $totalVAT = 0;
    $totalTTC = $totalHT;
    $pdo->prepare("UPDATE invoices SET total_ht = ?, total_vat = ?, total_ttc = ? WHERE id_invoice = ?")
        ->execute([$totalHT, $totalVAT, $totalTTC, $invoiceId]);

    $invoiceCounter++;
}

echo count($clients)*3 . " factures minimum générées pour chaque client + factures supplémentaires pour atteindre 90.\n";
echo "invoices_fixture.php terminé.\n";