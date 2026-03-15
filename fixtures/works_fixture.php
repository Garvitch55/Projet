<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config.php';

$pdo = getPDO();
$faker = Faker\Factory::create('fr_FR');

// Vider la table works
$pdo->exec("DELETE FROM works");

// Types d'ouvrages gros œuvre
$workTypes = [
    "Dalle béton", "Fondations maison", "Mur de soutènement", 
    "Terrassement", "Garage", "Extension maison", "Plancher béton",
    "Escalier béton", "Chape ciment", "Ouverture mur porteur", "Mur de clôture"
];

$stmt = $pdo->prepare("
    INSERT INTO works (name, description, unit, unit_price, created_at)
    VALUES (:name, :description, :unit, :unit_price, :created_at)
");

for ($i = 0; $i < 50; $i++) {
    $name = $faker->randomElement($workTypes) . " #" . ($i+1);
    $stmt->execute([
        'name' => $name,
        'description' => $faker->sentence(12),
        'unit' => $faker->randomElement(['m²', 'm³', 'unité']),
        'unit_price' => $faker->randomFloat(2, 50, 1000),
        'created_at' => $faker->dateTimeBetween('-6 months', 'now')->format('Y-m-d H:i:s')
    ]);
}

echo "1/ 50 ouvrages gros œuvre générés avec succès.\n";

