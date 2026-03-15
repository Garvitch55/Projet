<?php
// ---------------------------
// Fixture pour les lignes de devis (quote_items)
// ---------------------------

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config.php';

use Faker\Factory;

// ---------------------------
// Faker français
// ---------------------------
$faker = Factory::create('fr_FR');

try {
    $pdo = getPDO();

    echo "1/ Chargement de quote_items_fixture.php...\n";

    // ---------------------------
    // Vider la table quote_items en désactivant temporairement les clés étrangères
    // ---------------------------
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    $pdo->exec("DELETE FROM quote_items");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    echo "2/ Table quote_items vidée.\n";

    // ---------------------------
    // Récupérer tous les devis
    // ---------------------------
    $quotes = $pdo->query("SELECT id_quote FROM quotes")->fetchAll(PDO::FETCH_ASSOC);
    if (empty($quotes)) {
        die("Aucun devis trouvé. Générer d'abord les devis.\n");
    }

    // ---------------------------
    // Récupérer tous les ouvrages
    // ---------------------------
    $works = $pdo->query("SELECT id_work, name, unit_price FROM works")->fetchAll(PDO::FETCH_ASSOC);
    if (empty($works)) {
        die("Aucun ouvrage trouvé. Générer d'abord les ouvrages.\n");
    }

    // ---------------------------
    // Préparer l'insertion dans quote_items
    // ---------------------------
   $stmtItem = $pdo->prepare("
    INSERT INTO quote_items 
    (quote_id, work_id, description, quantity, unit_price, total_price, created_at)
    VALUES (:quote_id, :work_id, :description, :quantity, :unit_price, :total_price, :created_at)
");

    // ---------------------------
    // Préparer la mise à jour des totaux dans quotes
    // ---------------------------
    $stmtUpdateQuote = $pdo->prepare("
        UPDATE quotes 
        SET total_ht = :ht, total_vat = :vat, total_ttc = :ttc 
        WHERE id_quote = :id
    ");

    // ---------------------------
    // Générer les lignes de devis et calculer les totaux
    // ---------------------------
    foreach ($quotes as $quote) {
        $totalHT = 0;

        // Générer 1 à 5 lignes par devis
        $numItems = rand(1, 5);
        for ($i = 0; $i < $numItems; $i++) {
            $work = $faker->randomElement($works);
            $quantity = rand(1, 20);
            $unitPrice = $work['unit_price'];
            $totalPrice = $quantity * $unitPrice;

           $stmtItem->execute([
    'quote_id' => $quote['id_quote'],
    'work_id' => $work['id_work'],
    'description' => $work['name'],
    'quantity' => $quantity,
    'unit_price' => $unitPrice,
    'total_price' => $totalPrice,
    'created_at' => date('Y-m-d H:i:s')
]);

            $totalHT += $totalPrice;
        }

        $totalVAT = $totalHT * 0.20;
        $totalTTC = $totalHT + $totalVAT;

        $stmtUpdateQuote->execute([
            'ht' => $totalHT,
            'vat' => $totalVAT,
            'ttc' => $totalTTC,
            'id' => $quote['id_quote']
        ]);
    }

    echo "3/ Lignes de devis générées et totaux mis à jour.\n";
    echo "4/ quote_items_fixture.php terminé.\n";

} catch (PDOException $e) {
    echo "Erreur PDO : " . $e->getMessage() . "\n";
}