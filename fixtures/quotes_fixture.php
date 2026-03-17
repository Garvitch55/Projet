<?php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Faker\Factory;

$pdo = getPDO();
$faker = Factory::create('fr_FR');

// Vider quotes et quote_items
$pdo->exec("DELETE FROM quote_items");
$pdo->exec("DELETE FROM quotes");

echo "Chargement de quotes_fixture.php avec taux TVA aléatoire...\n";
$clients = $pdo->query("SELECT id_client FROM gestion_client")->fetchAll(PDO::FETCH_ASSOC);
echo "Nombre de clients trouvés : " . count($clients) . "\n";
// Clients
$clients = $pdo->query("SELECT id_client FROM gestion_client")->fetchAll(PDO::FETCH_ASSOC);
if (empty($clients)) die("Aucun client trouvé.\n");

// Ouvrages
$works = $pdo->query("SELECT id_work, name, unit_price FROM works")->fetchAll(PDO::FETCH_ASSOC);
if (empty($works)) die("Aucun ouvrage trouvé.\n");

// TVA disponibles
$tvas = $pdo->query("SELECT id_tva, rate FROM tva")->fetchAll(PDO::FETCH_ASSOC);
if (empty($tvas)) die("Aucune TVA trouvée.\n");

// Préparer l'insertion des devis
$stmtQuote = $pdo->prepare("
    INSERT INTO quotes
    (client_id, quote_number, quote_date, status, total_ht, total_vat, total_ttc, created_at)
    VALUES (:client_id, :quote_number, :quote_date, :status, :total_ht, :total_vat, :total_ttc, :created_at)
");

// Préparer l'insertion des lignes de devis
$stmtItem = $pdo->prepare("
    INSERT INTO quote_items
    (quote_id, work_id, description, quantity, unit_price, total_price, created_at)
    VALUES (:quote_id, :work_id, :description, :quantity, :unit_price, :total_price, :created_at)
");

$statuses = ['en attente', 'signé', 'annulé'];
$quoteCounter = 1;

// ✅ Boucle sur chaque client
foreach ($clients as $client) {
    // Minimum 3 devis par client
    for ($i = 0; $i < 3; $i++) {
        $status = $faker->randomElement($statuses);
        $tva = $faker->randomElement($tvas);
        $tvaRate = $tva['rate'];

        // Insérer le devis
        $stmtQuote->execute([
            'client_id'    => $client['id_client'],
            'quote_number' => 'Q-' . str_pad($quoteCounter, 4, '0', STR_PAD_LEFT),
            'quote_date'   => $faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'status'       => $status,
            'total_ht'     => 0,
            'total_vat'    => $tvaRate,  // juste pour pré-remplir
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

        // Mettre à jour le HT et TTC
        $totalVAT = $totalHT * ($tvaRate / 100);
        $totalTTC = $totalHT + $totalVAT;
        $pdo->prepare("UPDATE quotes SET total_ht = ?, total_vat = ?, total_ttc = ? WHERE id_quote = ?")
            ->execute([$totalHT, $totalVAT, $totalTTC, $quoteId]);

        $quoteCounter++;
    }
}

echo count($clients) * 3 . " devis générés avec succès (minimum 3 par client).\n";