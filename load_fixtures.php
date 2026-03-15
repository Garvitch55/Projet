<?php
// Chemin vers le dossier fixtures
$fixturesDir = __DIR__ . '/fixtures';

// Liste des fichiers à exécuter dans l'ordre correct
$fixtures = [
    'contact_fixture.php',
    'client_fixture.php',
    'works_fixture.php',       // 50 ouvrages gros œuvre
    'quotes_fixture.php',      // 30 devis liés aux clients existants
    'quote_items_fixture.php'  // lignes de devis et calculs totaux
];

foreach ($fixtures as $file) {
    $path = $fixturesDir . '/' . $file;

    if (!file_exists($path)) {
        echo "Fixture non trouvée : $file\n";
        continue;
    }

    echo "1/ Chargement de $file...\n";
    require $path;
    echo "2/ $file terminé.\n\n";
}

echo "yes!!! Toutes les fixtures ont été chargées avec succès !\n";