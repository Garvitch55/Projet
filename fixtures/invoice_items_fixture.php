<?php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Faker\Factory;

$pdo = getPDO();
$faker = Factory::create('fr_FR');

echo "Chargement de invoice_items_fixture.php...\n";

// ---------------------------
// Vider la table invoice_items
// ---------------------------
$pdo->exec("DELETE FROM invoice_items");

// ---------------------------
// Récupérer toutes les factures existantes
// ---------------------------
$invoices = $pdo->query("SELECT id_invoice FROM invoices")->fetchAll(PDO::FETCH_ASSOC);
if (empty($invoices)) die("Aucune facture trouvée. Générer d'abord les factures.\n");

// ---------------------------
// Récupérer tous les ouvrages existants
// ---------------------------
$works = $pdo->query("SELECT id_work, name, unit_price FROM works")->fetchAll(PDO::FETCH_ASSOC);
if (empty($works)) die("Aucun ouvrage trouvé. Générer d'abord les ouvrages.\n");

// ---------------------------
// Préparer l'insertion des lignes de facture
// ---------------------------
$stmtItem = $pdo->prepare("
    INSERT INTO invoice_items
    (invoice_id, work_id, description, quantity, unit_price, total_price, created_at)
    VALUES (:invoice_id, :work_id, :description, :quantity, :unit_price, :total_price, :created_at)
");

// ---------------------------
// Générer 1 à 5 lignes par facture
// ---------------------------
foreach ($invoices as $invoice) {
    $invoiceId = $invoice['id_invoice'];
    $nbLines = $faker->numberBetween(1, 5);

    for ($i = 0; $i < $nbLines; $i++) {
        $work = $faker->randomElement($works);
        $quantity = $faker->numberBetween(1, 10);
        $lineTotal = $work['unit_price'] * $quantity;

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
}

echo "Lignes de facture générées avec succès pour toutes les factures existantes.\n";
echo "invoice_items_fixture.php terminé.\n";
