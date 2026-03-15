<?php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Faker\Factory;

$pdo = getPDO();
$faker = Factory::create('fr_FR');

// ---------------------------
// Vider les tables quotes et quote_items
// ---------------------------
$pdo->exec("DELETE FROM quote_items");
$pdo->exec("DELETE FROM quotes");

echo "Chargement de quotes_fixture.php (prix HT uniquement)...\n";

// ---------------------------
// Récupérer tous les clients
// ---------------------------
$clients = $pdo->query("SELECT id_client FROM gestion_client")->fetchAll(PDO::FETCH_ASSOC);
if (empty($clients)) die("Aucun client trouvé. Générer d'abord les clients.\n");

// ---------------------------
// Récupérer tous les ouvrages
// ---------------------------
$works = $pdo->query("SELECT id_work, name, unit_price FROM works")->fetchAll(PDO::FETCH_ASSOC);
if (empty($works)) die("Aucun ouvrage trouvé. Générer d'abord les ouvrages.\n");

// ---------------------------
// Préparer la requête pour les devis
// ---------------------------
$stmtQuote = $pdo->prepare("
    INSERT INTO quotes 
    (client_id, quote_number, quote_date, status, total_ht, total_vat, total_ttc, created_at)
    VALUES (:client_id, :quote_number, :quote_date, :status, :total_ht, :total_vat, :total_ttc, :created_at)
");

// ---------------------------
// Préparer la requête pour les lignes d’ouvrages
// ---------------------------
$stmtItem = $pdo->prepare("
    INSERT INTO quote_items
    (quote_id, work_id, description, quantity, unit_price, total_price, created_at)
    VALUES (:quote_id, :work_id, :description, :quantity, :unit_price, :total_price, :created_at)
");

// ---------------------------
// Statuts en français
// ---------------------------
$statuses = ['en attente', 'signé', 'annulé'];

// ---------------------------
// Générer 30 devis avec lignes
// ---------------------------
for ($i = 0; $i < 30; $i++) {
    $client = $faker->randomElement($clients);
    $status = $faker->randomElement($statuses);

    // Insérer le devis avec totaux initiaux à 0
    $stmtQuote->execute([
        'client_id'    => $client['id_client'],
        'quote_number' => 'Q-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
        'quote_date'   => $faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
        'status'       => $status,
        'total_ht'     => 0,
        'total_vat'    => 0,
        'total_ttc'    => 0,
        'created_at'   => date('Y-m-d H:i:s')
    ]);

    $quoteId = $pdo->lastInsertId();

    $nbLines = $faker->numberBetween(1, 5);
    $totalHT = 0;

    for ($j = 0; $j < $nbLines; $j++) {
        $work = $faker->randomElement($works);
        $quantity = $faker->numberBetween(1, 10);

        $lineTotal = $work['unit_price'] * $quantity;
        $totalHT += $lineTotal;

        // Insérer la ligne
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

    // Total TTC = Total HT car pas de TVA
    $totalVAT = 0;
    $totalTTC = $totalHT;

    // Mettre à jour le devis avec HT et TTC
    $pdo->prepare("UPDATE quotes SET total_ht = ?, total_vat = ?, total_ttc = ? WHERE id_quote = ?")
        ->execute([$totalHT, $totalVAT, $totalTTC, $quoteId]);
}

echo "30 devis générés avec succès (prix HT uniquement, TVA = 0).\n";
echo "quotes_fixture.php terminé.\n";