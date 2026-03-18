<?php
require_once __DIR__ . '/../config.php';

$pdo = getPDO();

// Vider la table avant insertion (optionnel)
$pdo->exec("TRUNCATE TABLE reference_management");

// Liste des références à insérer
$references = [
    [
        'name' => 'École Primaire Jean Moulin',
        'site' => 'Paris 12ème',
        'description' => 'Construction d\'une école primaire moderne avec 12 salles de classe et espaces sportifs.',
        'image' => 'ecole_jean_moulin.jpg',
        'Completion_date' => '2024-06-15'
    ],
    [
        'name' => 'École Maternelle Les Petits Loups',
        'site' => 'Lyon 3ème',
        'description' => 'Construction d\'une maternelle accueillante avec cour de récréation sécurisée.',
        'image' => 'maternelle_petits_loups.jpg',
        'Completion_date' => '2023-11-30'
    ],
    [
        'name' => 'Maison Individuelle Famille Dupont',
        'site' => 'Villeurbanne',
        'description' => 'Maison individuelle moderne de 120m² avec jardin et garage.',
        'image' => 'maison_dupont.jpg',
        'Completion_date' => '2023-08-20'
    ],
    [
        'name' => 'Résidence Collective Les Horizons',
        'site' => 'Marseille 6ème',
        'description' => 'Construction d\'un bâtiment collectif de 24 appartements avec parkings souterrains.',
        'image' => 'residence_horizons.jpg',
        'Completion_date' => '2024-01-10'
    ],
    [
        'name' => 'Démolition Immeuble Rue de la Paix',
        'site' => 'Nice',
        'description' => 'Démolition complète d\'un ancien immeuble pour reconstruction future.',
        'image' => 'demolition_p.jpg',
        'Completion_date' => '2023-05-05'
    ],
    [
        'name' => 'École Primaire Saint-Exupéry',
        'site' => 'Toulouse',
        'description' => 'Construction d\'une école primaire avec bibliothèque et salle informatique.',
        'image' => 'ecole_saintex.jpg',
        'Completion_date' => '2024-02-28'
    ],
    [
        'name' => 'Maison Individuelle Famille Martin',
        'site' => 'Bordeaux',
        'description' => 'Maison contemporaine avec 3 chambres et piscine extérieure.',
        'image' => 'maison_martin.jpg',
        'Completion_date' => '2023-09-15'
    ],
    [
        'name' => 'Résidence Collective Le Parc',
        'site' => 'Lille',
        'description' => 'Bâtiment collectif de 18 logements sociaux avec espaces verts.',
        'image' => 'residence_parc.jpg',
        'Completion_date' => '2024-03-22'
    ],
    [
        'name' => 'Démolition Ancien Entrepôt',
        'site' => 'Nantes',
        'description' => 'Démolition d\'un ancien entrepôt industriel pour libérer le terrain.',
        'image' => 'demolition_entrepot.jpg',
        'Completion_date' => '2023-07-18'
    ],
    [
        'name' => 'École Maternelle Arc-en-Ciel',
        'site' => 'Strasbourg',
        'description' => 'Construction d\'une maternelle colorée avec salle polyvalente et jardin pédagogique.',
        'image' => 'maternelle_arc.jpg',
        'Completion_date' => '2024-05-12'
    ],
];

// Insertion des références dans la base
$stmt = $pdo->prepare("INSERT INTO reference_management (name, site, description, image, Completion_date, created_at) VALUES (:name, :site, :description, :image, :Completion_date, NOW())");

foreach ($references as $ref) {
    $stmt->execute([
        ':name' => $ref['name'],
        ':site' => $ref['site'],
        ':description' => $ref['description'],
        ':image' => $ref['image'],
        ':Completion_date' => $ref['Completion_date']
    ]);
}

echo "Fixture insérée avec succès !";