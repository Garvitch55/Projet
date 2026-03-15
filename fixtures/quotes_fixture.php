<?php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Faker\Factory;

// -----------------------------
// Connexion PDO avec utf8mb4
// -----------------------------
$pdo = getPDO(); // Assure-toi que getPDO() utilise charset=utf8mb4
$faker = Factory::create('fr_FR');

// -----------------------------
// Vider la table quotes avant insertion
// -----------------------------
$pdo->exec("DELETE FROM quotes");
echo "Chargement de quotes_fixture.php...\n";

// -----------------------------
// Récupérer tous les clients
// -----------------------------
$clients = $pdo->query("SELECT id_client FROM gestion_client")->fetchAll(PDO::FETCH_ASSOC);
if (empty($clients)) die("Aucun client trouvé. Générer d'abord les clients.\n");

// -----------------------------
// Préparer la requête d'insertion
// -----------------------------
$stmt = $pdo->prepare("
    INSERT INTO quotes 
    (client_id, quote_number, quote_date, status, total_ht, total_vat, total_ttc, created_at)
    VALUES (:client_id, :quote_number, :quote_date, :status, :total_ht, :total_vat, :total_ttc, :created_at)
");

// -----------------------------
// Statuts en français
// -----------------------------
$statuses = ['en attente', 'signé', 'annulé'];

// -----------------------------
// Génération de 30 devis
// -----------------------------
for ($i = 0; $i < 30; $i++) {
    $client = $faker->randomElement($clients);
    $status = $faker->randomElement($statuses);

    $stmt->execute([
        'client_id'    => $client['id_client'],
        'quote_number' => 'Q-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
        'quote_date'   => $faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
        'status'       => $status,
        'total_ht'     => 0,
        'total_vat'    => 0,
        'total_ttc'    => 0,
        'created_at'   => date('Y-m-d H:i:s')
    ]);
}

echo "30 devis générés avec succès avec statuts en français.\n";
echo "quotes_fixture.php terminé.\n";