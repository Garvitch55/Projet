<?php

// ---------------------------
// Fixture personnel
// ---------------------------

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Faker\Factory;

// ---------------------------
// Session uniquement si web
// ---------------------------
if (php_sapi_name() !== 'cli' && session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Faker FR
$faker = Factory::create('fr_FR');

try {
    $pdo = getPDO();

    echo "1/ Chargement de personnel_fixture.php...\n";

    // ---------------------------
    // Vider la table
    // ---------------------------
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    $pdo->exec("DELETE FROM gestion_personnel");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

    echo "2/ Table gestion_personnel vidée.\n";

    // ---------------------------
    // Préparer la requête
    // ---------------------------
    $stmt = $pdo->prepare("
        INSERT INTO gestion_personnel 
        (firstname, lastname, mail, birthdate, phone, rue, cp, ville, password, fonction) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    // ---------------------------
    // Début transaction
    // ---------------------------
    $pdo->beginTransaction();

    // ---------------------------
    // ADMIN 1 : Adrien
    // ---------------------------
    $stmt->execute([
        'Adrien',
        'Garnier',
        'adrien-garnier@orange.fr',
        null,
        '062424584184',
        '15 allée du pré l\'évêque',
        '55100',
        'Verdun',
        password_hash('Garvitch_55100', PASSWORD_DEFAULT),
        'administrateur'
    ]);

    // ---------------------------
    // ADMIN 2 : Jury
    // ---------------------------
    $stmt->execute([
        'Jury',
        'Web Dev',
        'jury-web@dev.fr',
        null,
        '0600000000',
        'rue de l\'ALAJI',
        '55100',
        'Verdun',
        password_hash('Jury_web', PASSWORD_DEFAULT),
        'administrateur'
    ]);

    // ---------------------------
    // 20 EMPLOYÉS FAKE
    // ---------------------------
    for ($i = 0; $i < 20; $i++) {
        $stmt->execute([
            $faker->firstName(),
            $faker->lastName(),
            $faker->unique()->safeEmail(),
            $faker->date('Y-m-d', '2004-01-01'),
            $faker->phoneNumber(),
            $faker->streetAddress(),
            $faker->postcode(),
            $faker->city(),
            password_hash('Password123', PASSWORD_DEFAULT),
            'employé'
        ]);
    }

    // ---------------------------
    // Commit
    // ---------------------------
    $pdo->commit();

    echo "3/ 2 administrateurs + 20 employés insérés !\n";
    echo "4/ personnel_fixture.php terminé.\n";

} catch (PDOException $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    echo "Erreur PDO : " . $e->getMessage() . "\n";
}