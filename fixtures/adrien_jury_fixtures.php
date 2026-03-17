<?php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Faker\Factory;

$pdo = getPDO();
$faker = Factory::create('fr_FR');

echo "Chargement de fixtures clients, devis et factures pour Adrien et Jury...\n";

// ---------------------------
// Récupérer uniquement Adrien et Jury
// ---------------------------
$clients = $pdo->query("
    SELECT id_client, firstname FROM gestion_client
    WHERE firstname IN ('Adrien','Jury')
")->fetchAll(PDO::FETCH_ASSOC);

if (empty($clients)) die("Aucun client trouvé.\n");

// ---------------------------
// Récupérer tous les ouvrages existants
// ---------------------------
$works = $pdo->query("SELECT id_work, name, unit_price FROM works")->fetchAll(PDO::FETCH_ASSOC);
if (empty($works)) die("Aucun ouvrage trouvé.\n");

// ---------------------------
// Préparer l'insertion des devis et items
// ---------------------------
$stmtQuote = $pdo->prepare("
    INSERT INTO quotes
    (client_id, quote_number, quote_date, status, total_ht, total_vat, total_ttc, created_at)
    VALUES (:client_id, :quote_number, :quote_date, :status, :total_ht, :total_vat, :total_ttc, :created_at)
");

$stmtQuoteItem = $pdo->prepare("
    INSERT INTO quote_items
    (quote_id, work_id, description, quantity, unit_price, total_price, created_at)
    VALUES (:quote_id, :work_id, :description, :quantity, :unit_price, :total_price, :created_at)
");

// ---------------------------
// Générer 30 devis par client
// ---------------------------
$statusesQuotes = ['en attente', 'signé', 'annulé'];
$quoteCounter = 1;

foreach ($clients as $client) {
    for ($i = 0; $i < 30; $i++) {
        $status = $faker->randomElement($statusesQuotes);
        $quoteDate = $faker->dateTimeBetween('-1 year','now')->format('Y-m-d');

        // Insérer le devis
        $stmtQuote->execute([
            'client_id'    => $client['id_client'],
            'quote_number' => strtoupper(substr($client['firstname'],0,2)).'-Q'.str_pad($quoteCounter,3,'0',STR_PAD_LEFT),
            'quote_date'   => $quoteDate,
            'status'       => $status,
            'total_ht'     => 0,
            'total_vat'    => 0,
            'total_ttc'    => 0,
            'created_at'   => date('Y-m-d H:i:s')
        ]);

        $quoteId = $pdo->lastInsertId();
        $totalHT = 0;

        // 1 à 5 lignes par devis
        $nbLines = $faker->numberBetween(1,5);
        for ($j=0; $j<$nbLines; $j++) {
            $work = $faker->randomElement($works);
            $quantity = $faker->numberBetween(1,10);
            $lineTotal = $work['unit_price'] * $quantity;
            $totalHT += $lineTotal;

            $stmtQuoteItem->execute([
                'quote_id'    => $quoteId,
                'work_id'     => $work['id_work'],
                'description' => $work['name'],
                'quantity'    => $quantity,
                'unit_price'  => $work['unit_price'],
                'total_price' => $lineTotal,
                'created_at'  => date('Y-m-d H:i:s')
            ]);
        }

        // Mettre à jour les totaux
        $totalTTC = $totalHT; // TVA à 0 pour simplifier
        $pdo->prepare("UPDATE quotes SET total_ht = ?, total_vat = ?, total_ttc = ? WHERE id_quote = ?")
            ->execute([$totalHT, 0, $totalTTC, $quoteId]);

        $quoteCounter++;
    }
}

echo "1/ 30 devis générés pour chaque client.\n";

// ---------------------------
// Préparer l'insertion des factures et items
// ---------------------------
$stmtInvoice = $pdo->prepare("
    INSERT INTO invoices
    (quote_id, client_id, invoice_number, invoice_date, due_date, status, total_ht, total_vat, total_ttc, created_at)
    VALUES (:quote_id, :client_id, :invoice_number, :invoice_date, :due_date, :status, :total_ht, :total_vat, :total_ttc, :created_at)
");

$stmtInvoiceItem = $pdo->prepare("
    INSERT INTO invoice_items
    (invoice_id, work_id, description, quantity, unit_price, total_price, created_at)
    VALUES (:invoice_id, :work_id, :description, :quantity, :unit_price, :total_price, :created_at)
");

$statusesInvoices = ['brouillon','en attente de paiement','payée','annulée'];
$invoiceCounter = 1;

// ---------------------------
// Générer 20 factures par client
// ---------------------------
foreach ($clients as $client) {
    for ($i=0; $i<20; $i++) {
        // Lier à un devis aléatoire existant
        $quoteId = $pdo->query("SELECT id_quote FROM quotes WHERE client_id=".$client['id_client']." ORDER BY RAND() LIMIT 1")->fetchColumn();

        $invoiceDate = $faker->dateTimeBetween('-1 year','now')->format('Y-m-d');
        $dueDate = date('Y-m-d', strtotime($invoiceDate.' +30 days'));
        $status = $faker->randomElement($statusesInvoices);

        $stmtInvoice->execute([
            'quote_id'       => $quoteId,
            'client_id'      => $client['id_client'],
            'invoice_number' => strtoupper(substr($client['firstname'],0,2)).'-I'.str_pad($invoiceCounter,3,'0',STR_PAD_LEFT),
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

        // 1 à 5 lignes par facture
        $nbLines = $faker->numberBetween(1,5);
        for ($j=0; $j<$nbLines; $j++) {
            $work = $faker->randomElement($works);
            $quantity = $faker->numberBetween(1,10);
            $lineTotal = $work['unit_price'] * $quantity;
            $totalHT += $lineTotal;

            $stmtInvoiceItem->execute([
                'invoice_id'  => $invoiceId,
                'work_id'     => $work['id_work'],
                'description' => $work['name'],
                'quantity'    => $quantity,
                'unit_price'  => $work['unit_price'],
                'total_price' => $lineTotal,
                'created_at'  => date('Y-m-d H:i:s')
            ]);
        }

        // Mettre à jour totaux
        $totalTTC = $totalHT;
        $pdo->prepare("UPDATE invoices SET total_ht = ?, total_vat = ?, total_ttc = ? WHERE id_invoice = ?")
            ->execute([$totalHT, 0, $totalTTC, $invoiceId]);

        $invoiceCounter++;
    }
}

echo "2/ 20 factures générées pour chaque client.\n";
echo "3/ Fixtures terminées.\n";