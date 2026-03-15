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

for ($i = 0; $i < 30; $i++) {
    $client = $faker->randomElement($clients);
    $status = $faker->randomElement($statuses);

    // Choisir aléatoirement un taux de TVA (juste le rate)
    $tva = $faker->randomElement($tvas);
    $tvaRate = $tva['rate'];

    // Insérer le devis avec HT = 0 pour l'instant et total_vat = taux
    $stmtQuote->execute([
        'client_id'    => $client['id_client'],
        'quote_number' => 'Q-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
        'quote_date'   => $faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
        'status'       => $status,
        'total_ht'     => 0,
        'total_vat'    => $tvaRate,  // juste pour pré-remplir le select
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
            'total_price' => $lineTotal, // juste HT
            'created_at'  => date('Y-m-d H:i:s')
        ]);
    }

    // Mettre à jour le HT dans le devis
    $pdo->prepare("UPDATE quotes SET total_ht = ? WHERE id_quote = ?")
        ->execute([$totalHT, $quoteId]);
}

echo "30 devis générés avec succès avec taux TVA aléatoire (5.5, 10, 20, 0) pour affichage.\n";
echo "quotes_fixture.php terminé.\n";