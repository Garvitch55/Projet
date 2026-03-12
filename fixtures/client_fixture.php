<?php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Faker\Factory;

// Démarrage de session si nécessaire
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$faker = Factory::create('fr_FR'); // Locale française

try {
    $pdo = getPDO();

    // Préparer la requête pour insertion dans gestion_client
    $stmt = $pdo->prepare("
        INSERT INTO gestion_client 
        (firstname, lastname, birthdate, phone, email, rue, cp, ville, demande, password) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $pdo->beginTransaction();

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
    echo "200 clients ont été insérés avec succès !";

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "Erreur : " . $e->getMessage();
}