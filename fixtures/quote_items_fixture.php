<?php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Faker\Factory;

$pdo = getPDO();
$faker = Factory::create('fr_FR');

// Vider les tables
$pdo->exec("DELETE FROM quote_items");
$pdo->exec("DELETE FROM quotes");

echo "Chargement de quotes_fixture.php (prix HT uniquement)...\n";

// Récupérer tous les clients
$clients = $pdo->query("SELECT id_client FROM gestion_client")->fetchAll(PDO::FETCH_ASSOC);
if (empty($clients)) die("Aucun client trouvé. Générer d'abord les clients.\n");

// Récupérer tous les ouvrages
$works = $pdo->query("SELECT id_work, name, unit_price FROM works")->fetchAll(PDO::FETCH_ASSOC);
if (empty($works)) die("Aucun ouvrage trouvé. Générer d'abord les ouvrages.\n");

// Préparer l'insertion des devis
$stmtQuote = $pdo->prepare("
    INSERT INTO quotes 
    (client_id, quote_number, quote_date, status, total_ht, total_vat, total_ttc, created_at)
    VALUES (:client_id, :quote_number, :quote_date, :status, :total_ht, :total_vat, :total_ttc, :created_at)
");

// Préparer l'insertion des lignes
$stmtItem = $pdo->prepare("
    INSERT INTO quote_items
    (quote_id, work_id, description, quantity, unit_price, total_price, created_at)
    VALUES (:quote_id, :work_id, :description, :quantity, :unit_price, :total_price, :created_at)
");

$statuses = ['en attente', 'signé', 'annulé'];
$quoteCounter = 1;

// Boucle sur chaque client
foreach ($clients as $client) {
    for ($i = 0; $i < 3; $i++) { // minimum 3 devis par client
        $status = $faker->randomElement($statuses);

        // Insérer le devis
        $stmtQuote->execute([
            'client_id'    => $client['id_client'],
            'quote_number' => 'Q-' . str_pad($quoteCounter, 4, '0', STR_PAD_LEFT),
            'quote_date'   => $faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'status'       => $status,
            'total_ht'     => 0,
            'total_vat'    => 0,
            'total_ttc'    => 0,
            'created_at'   => date('Y-m-d H:i:s')
        ]);

        $quoteId = $pdo->lastInsertId();
        $totalHT = 0;

        $nbLines = $faker->numberBetween(1, 5);

        for ($j = 0; $j < $nbLines; $j++) {
            $work = $faker->randomElement($works);
            $quantity = $faker->numberBetween(1, 10);
            $lineTotal = $work['unit_price'] * $quantity;
            $totalHT += $lineTotal;

            $stmtItem->execute([
                'quote_id'    => $quoteId,
                'work_id'     => $work['id_work'],
                'description' => $work['name'],
                'quantity'    => $quantity,
                'unit_price'  => $work['unit_price'],
                'total_price' => $lineTotal,
                'created_at'  => date('Y-m-d H:i:s')
            ]);
        }

        // Mettre à jour le devis avec HT et TTC
        $totalVAT = 0; // pas de TVA
        $totalTTC = $totalHT;
        $pdo->prepare("UPDATE quotes SET total_ht = ?, total_vat = ?, total_ttc = ? WHERE id_quote = ?")
            ->execute([$totalHT, $totalVAT, $totalTTC, $quoteId]);

        $quoteCounter++;
    }
}

echo count($clients) * 3 . " devis générés avec succès (minimum 3 par client, prix HT uniquement).\n";
echo "quotes_fixture.php terminé.\n";