<?php
// ---------------------------
// Fixture réponses clients (client + personnel)
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
    // Récupérer tous les clients et personnels
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
        (client_id, message, reply_client, reply_personnel, personnel_id, is_read, created_at, response_date) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $pdo->beginTransaction();

    // ---------------------------
    // Contenus de réponses réalistes
    // ---------------------------
    $reponses_personnel = [
        "Bonjour, nous avons bien reçu votre demande et reviendrons vers vous rapidement.",
        "Merci pour votre message. Nous pouvons commencer les travaux dès la semaine prochaine.",
        "Votre devis est en cours de préparation. Vous recevrez la confirmation sous peu.",
        "Nous avons pris en compte votre demande et vous contacterons pour préciser les détails.",
        "Votre chantier peut être planifié pour le mois prochain. Merci pour votre patience."
    ];

    $reponses_client = [
        "Merci pour votre retour, je reste disponible.",
        "Parfait, j'attends votre devis.",
        "Je confirme la réception de votre message.",
        "D'accord, merci pour les informations.",
        "Merci beaucoup, je vous tiens informé."
    ];

    // ---------------------------
    // Générer des réponses aléatoires entre 2 et 5 par client
    // ---------------------------
    foreach ($clients as $client) {
        $nbReplies = rand(2, 5); // entre 2 et 5 réponses

        for ($i = 0; $i < $nbReplies; $i++) {
            $personnel = $personnels ? $faker->randomElement($personnels) : null;
            $personnel_id = $personnel['id_personnel'] ?? null;

            $is_read = (int)$faker->boolean(50);

            // La date doit être après la création du client
            $created_at = $faker->dateTimeBetween($client['created_at'], 'now');
            $response_date = $created_at;

            // Choisir aléatoirement si c'est une réponse du personnel ou du client
            $reply_personnel = $faker->randomElement($reponses_personnel);
            $reply_client = $faker->randomElement($reponses_client);

            // Le message initial sera le même que la réponse personnel pour le test
            $message = $reply_personnel;

            $stmt->execute([
                $client['id_client'],
                $message,
                $reply_client,
                $reply_personnel,
                $personnel_id,
                $is_read,
                $created_at->format('Y-m-d H:i:s'),
                $response_date->format('Y-m-d H:i:s')
            ]);
        }
    }

    $pdo->commit();
    echo "3/ Réponses générées pour les clients avec réponses client et personnel.\n";

} catch (PDOException | Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "Erreur : " . $e->getMessage() . "\n";
}