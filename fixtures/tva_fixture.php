<?php

require_once __DIR__ . '/../config.php';

$pdo = getPDO();

echo "Chargement tva_fixture...\n";

$pdo->exec("DELETE FROM tva");

$tvas = [
    [
        'name' => 'TVA normale',
        'rate' => 20.00,
        'description' => 'Travaux de construction neuve'
    ],
    [
        'name' => 'TVA intermédiaire',
        'rate' => 10.00,
        'description' => 'Travaux de rénovation logement > 2 ans'
    ],
    [
        'name' => 'TVA réduite',
        'rate' => 5.50,
        'description' => 'Travaux de rénovation énergétique'
    ],
    [
        'name' => 'TVA exonérée',
        'rate' => 0.00,
        'description' => 'Cas particuliers ou exonération'
    ]
];

$stmt = $pdo->prepare("
    INSERT INTO tva (name, rate, description)
    VALUES (:name, :rate, :description)
");

foreach ($tvas as $tva) {
    $stmt->execute($tva);
}

echo "TVA insérées avec succès.\n";