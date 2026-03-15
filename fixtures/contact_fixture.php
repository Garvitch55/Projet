<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config.php';

$pdo = getPDO();

// Faker français
$faker = Faker\Factory::create('fr_FR');

// Liste de sujets réalistes pour le gros œuvre
$subjects = [
    "Demande de devis pour dalle béton",
    "Projet de construction maison",
    "Terrassement terrain",
    "Fondations pour maison individuelle",
    "Extension maison",
    "Création d’un garage",
    "Ouverture mur porteur",
    "Construction mur de clôture",
    "Chantier maçonnerie",
    "Réalisation plancher béton",
    "Rénovation gros œuvre",
    "Création dalle terrasse",
    "Travaux de maçonnerie générale"
];

// Messages réalistes clients
$messages = [
    "Bonjour, je souhaite obtenir un devis pour la réalisation d’une dalle béton pour une terrasse d’environ 40m².",
    "Nous avons un projet de construction de maison et cherchons une entreprise pour réaliser le gros œuvre.",
    "Pouvez-vous intervenir pour des travaux de terrassement sur un terrain situé en périphérie de la ville ?",
    "Je souhaiterais un devis pour l'ouverture d'un mur porteur dans ma maison.",
    "Nous aimerions construire un garage accolé à notre maison. Pouvez-vous nous contacter ?",
    "J’aurais besoin d’un devis pour la réalisation des fondations d’une maison individuelle.",
    "Nous envisageons une extension de 25m² et cherchons un maçon pour le gros œuvre.",
    "Merci de me contacter pour discuter d’un projet de mur de clôture autour de mon terrain.",
    "Je souhaiterais connaître vos délais pour un chantier de maçonnerie prévu cet été.",
    "Pouvez-vous intervenir pour refaire une dalle béton dans un garage ?"
];

// Optionnel : vider la table
$pdo->exec("DELETE FROM contact");

$stmt = $pdo->prepare("
    INSERT INTO contact 
    (first_name, last_name, email, phone, subject, message, is_read, created_at)
    VALUES 
    (:first_name, :last_name, :email, :phone, :subject, :message, :is_read, :created_at)
");

for ($i = 0; $i < 50; $i++) {

    $stmt->execute([
        'first_name' => $faker->firstName(),
        'last_name' => $faker->lastName(),
        'email' => $faker->safeEmail(),
        'phone' => $faker->phoneNumber(),
        'subject' => $subjects[array_rand($subjects)],
        'message' => $messages[array_rand($messages)],
        'is_read' => rand(0,1), // lu ou non lu
        'created_at' => $faker->dateTimeBetween('-30 days', 'now')->format('Y-m-d H:i:s')
    ]);
}

echo "1/ 50 messages clients BTP générés avec succès.";