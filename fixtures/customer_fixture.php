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
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0"); // désactiver temporairement les FK
    $pdo->exec("DELETE FROM gestion_client"); // supprimer toutes les entrées
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1"); // réactiver les FK
    echo "2/ Table gestion_client vidée.\n";

    // ---------------------------
    // Préparer la requête pour insertion dans gestion_client
    // ---------------------------
    $stmt = $pdo->prepare("
        INSERT INTO gestion_client 
        (firstname, lastname, birthdate, phone, email, rue, cp, ville, demande, password) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    // ---------------------------
    // Début transaction pour performance
    // ---------------------------
    $pdo->beginTransaction();

// ---------------------------
// INSERT ADMIN PAR DEFAUT
// ---------------------------

$stmt->execute([
    'Adrien',
    'Garnier',
    null, // birthdate
    '062424584184',
    'adrien-garnier1@orange.fr',
    '15 allée du pré l\'évêque',
    '55100',
    'Verdun',
    'Compte client administrateur',
    password_hash('Garvitch_55100', PASSWORD_DEFAULT)
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
    password_hash('Jury_web', PASSWORD_DEFAULT)
]);









    for ($i = 0; $i < 200; $i++) {
        $firstname = $faker->firstName();
        $lastname = $faker->lastName();
        $birthdate = $faker->date('Y-m-d', '2004-01-01'); // Clients >18 ans
        $phone = $faker->phoneNumber();
        $email = $faker->unique()->safeEmail();
        $rue = $faker->streetAddress();
        $cp = $faker->postcode();
        $ville = $faker->city();
        $demande = $faker->sentence(10);
        $password = password_hash('Password123', PASSWORD_DEFAULT); // mot de passe par défaut

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
            $password
        ]);
    }

    $pdo->commit();

    echo "3/ 200 clients ont été insérés avec succès !\n";
    echo "4/ client_fixture.php terminé.\n";

} catch (PDOException $e) {
    // ---------------------------
    // Rollback en cas d'erreur
    // ---------------------------
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "Erreur PDO : " . $e->getMessage() . "\n";
}