<?php
// ---------------------------
// Fixture réponses clients
// ---------------------------

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Faker\Factory;

// ---------------------------
// Faker français
// ---------------------------
$faker = Factory::create('fr_FR');

try {
    $pdo = getPDO();

    echo "1/ Chargement de client_replies_fixture.php...\n";

    // ---------------------------
    // Vider la table avant de réinsérer
    // ---------------------------
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    $pdo->exec("DELETE FROM client_replies");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    echo "2/ Table client_replies vidée.\n";

    // ---------------------------
    // Récupérer tous les clients existants
    // ---------------------------
    $clients = $pdo->query("SELECT id_client, created_at FROM gestion_client")->fetchAll(PDO::FETCH_ASSOC);
    $personnels = $pdo->query("SELECT id_personnel, firstname, lastname FROM gestion_personnel")->fetchAll(PDO::FETCH_ASSOC);

    if (!$clients) {
        throw new Exception("Aucun client trouvé pour générer des réponses.");
    }

    // ---------------------------
    // Préparer la requête d'insertion
    // ---------------------------
    $stmt = $pdo->prepare("
        INSERT INTO client_replies 
        (client_id, message, personnel_id, is_read, created_at, response_date) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $pdo->beginTransaction();

    // ---------------------------
    // Contenus de réponses réalistes gros œuvre
    // ---------------------------
    $reponses = [
        "Bonjour, nous avons bien reçu votre demande et reviendrons vers vous rapidement.",
        "Merci pour votre message. Nous pouvons commencer les travaux dès la semaine prochaine.",
        "Votre devis est en cours de préparation. Vous recevrez la confirmation sous peu.",
        "Nous avons pris en compte votre demande et vous contacterons pour préciser les détails.",
        "Votre chantier peut être planifié pour le mois prochain. Merci pour votre patience.",
        "Nous avons vérifié les fondations et tout est conforme. Nous attendons votre confirmation.",
        "Votre demande de dalle béton a été transmise à notre équipe technique.",
        "Les travaux de maçonnerie peuvent débuter dès que le matériel est livré."
    ];

    // ---------------------------
    // Générer des réponses aléatoires entre 2 et 5 par client
    // ---------------------------
    foreach ($clients as $client) {
        $nbReplies = rand(2, 5); // <-- entre 2 et 5 réponses

        for ($i = 0; $i < $nbReplies; $i++) {
            $personnel = $personnels ? $faker->randomElement($personnels) : null;
            $personnel_id = $personnel['id_personnel'] ?? null;

            $is_read = (int)$faker->boolean(50);

            // La réponse doit être après la date de création du client
            $created_at = $faker->dateTimeBetween($client['created_at'], 'now');
            $response_date = $created_at;

            $message = $faker->randomElement($reponses);

            $stmt->execute([
                $client['id_client'],
                $message,
                $personnel_id,
                $is_read,
                $created_at->format('Y-m-d H:i:s'),
                $response_date->format('Y-m-d H:i:s')
            ]);
        }
    }

    $pdo->commit();
    echo "3/ Réponses générées pour les clients avec 2 à 5 réponses par message.\n";

} catch (PDOException | Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "Erreur : " . $e->getMessage() . "\n";
}