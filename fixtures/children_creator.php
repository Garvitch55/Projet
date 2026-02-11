<?php

require __DIR__ . '/../vendor/autoload.php';
include_once __DIR__ . '/../config.php';

// On va créer une variable qui va déclarer le nom d'utilisateur
// que l'ont va créer en un click

$NB_USERS = 200;

// On appelle PDO

$pdo = getPDO();

// On va appeler un objet faker qui permettra de générer
// des personnes aléatoires

$faker = Faker\Factory::create('fr_FR');

$origins = ['groland', 'chnord', 'gotham', 'boukistan', 'neverland'];

// On génère des fausses données
for ($i = 0 ; $i < $NB_USERS; $i++) {
    // Il choisi un sexe biologique
    $biosex = $faker->randomElement(['girl', 'boy']);
    // Il faut que le prénom soit genré, donc il a besoin du sexe biologique
    // Male et female sont dees valeurs qui appartiennent à faker pour genrer les prénoms
    $firstname = $faker->firstName($biosex === 'girl' ? 'female' : 'male');
    $lastname = $faker->lastName();
    $birthdate = $faker->dateTimeBetween('-17 years', 'now')->format('Y-m-d'); // entre 0 et 17 ans
    $origin = $faker->randomElement($origins);
    $description = $faker->paragraph(3);

    $sql = 'INSERT INTO child(firstname, lastname, biosex, birthdate, origin, description)
            VALUES (?, ?, ?, ?, ?, ?)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $firstname,
        $lastname,
        $biosex,
        $birthdate,
        $origin,
        $description,
    ]);
}

header('Location: /../views/children/children_list.php');
exit;
