<?php

// ---------------------------
// Fixture clients
// ---------------------------

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Faker\Factory;

// ---------------------------
// On ne démarre pas de session en CLI pour éviter les warnings
// ---------------------------
if (php_sapi_name() !== 'cli' && session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ---------------------------
// Faker français
// ---------------------------
$faker = Factory::create('fr_FR');

try {
    $pdo = getPDO();

    echo "1/ Chargement de client_fixture.php...\n";

    // ---------------------------
    // Vider la table avant de réinsérer
    // ---------------------------
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    $pdo->exec("DELETE FROM gestion_client");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    echo "2/ Table gestion_client vidée.\n";

    // ---------------------------
    // Liste de demandes réalistes (gros œuvre)
    // ---------------------------
    $demandes = [
        "Demande de devis pour construction d'une maison individuelle de 120m²",
        "Réalisation des fondations pour une extension de maison",
        "Travaux de terrassement pour terrain en pente",
        "Construction d'un mur porteur en béton",
        "Création d'une dalle béton pour garage",
        "Ouverture d'un mur porteur avec pose IPN",
        "Construction d'un bâtiment industriel (gros œuvre)",
        "Réalisation d'une chape pour maison neuve",
        "Travaux de maçonnerie pour rénovation complète",
        "Création d'une terrasse en béton",
        "Extension de maison de 30m² avec fondations",
        "Construction d'un mur de soutènement",
        "Réalisation d'un vide sanitaire",
        "Travaux de démolition avant reconstruction",
        "Création d'une dalle pour piscine",
        "Maçonnerie générale pour maison neuve",
        "Réalisation d'un plancher béton",
        "Construction d'un garage en parpaing",
        "Travaux de fondation pour immeuble",
        "Demande de devis pour gros œuvre complet"
    ];

    // ---------------------------
    // Préparer la requête
    // ---------------------------
    $stmt = $pdo->prepare("
        INSERT INTO gestion_client 
        (firstname, lastname, birthdate, phone, email, rue, cp, ville, demande, password, is_read, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    // ---------------------------
    // Début transaction
    // ---------------------------
    $pdo->beginTransaction();

    // ---------------------------
    // INSERT ADMIN PAR DEFAUT
    // ---------------------------
    $stmt->execute([
        'Adrien',
        'Garnier',
        null,
        '062424584184',
        'adrien-garnier1@orange.fr',
        '15 allée du pré l\'évêque',
        '55100',
        'Verdun',
        'Compte client administrateur',
        password_hash('Garvitch_55100', PASSWORD_DEFAULT),
        1,
        date('Y-m-d H:i:s')
    ]);

    $stmt->execute([
        'Jury',
        'Web Dev',
        null,
        '0600000000',
        'jury-web1@dev.fr',
        'rue de l\'ALAJI',
        '55100',
        'Verdun',
        'Compte client administrateur',
        password_hash('Jury_web', PASSWORD_DEFAULT),
        1,
        date('Y-m-d H:i:s')
    ]);

    // ---------------------------
    // Générer 200 clients
    // ---------------------------
    for ($i = 0; $i < 200; $i++) {

        $firstname = $faker->firstName();
        $lastname = $faker->lastName();
        $birthdate = $faker->date('Y-m-d', '2004-01-01');
        $phone = $faker->phoneNumber();
        $email = $faker->unique()->safeEmail();
        $rue = $faker->streetAddress();
        $cp = $faker->postcode();
        $ville = $faker->city();

        $demande = $faker->randomElement($demandes);

        $password = password_hash('Password123', PASSWORD_DEFAULT);
        $is_read = (int) $faker->boolean(50);

        $created_at = $faker->dateTimeBetween('-6 months', 'now')->format('Y-m-d H:i:s');

        $stmt->execute([
            $firstname,
            $lastname,
            $birthdate,
            $phone,
            $email,
            $rue,
            $cp,
            $ville,
            $demande,
            $password,
            $is_read,
            $created_at
        ]);
    }

    $pdo->commit();

    echo "3/ 200 clients ont été insérés avec succès avec données réalistes !\n";
    echo "4/ client_fixture.php terminé.\n";

} catch (PDOException $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    echo "Erreur PDO : " . $e->getMessage() . "\n";
}