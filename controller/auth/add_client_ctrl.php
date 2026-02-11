<?php

// L'inport du PHP Data Object
// PDO sécurise la plus grosse faille d'internet : L'injection SQL
include_once '../../config.php';
// Fichier qui ajoute les clients dans la base de données

// echo '<pre>';
// var_dump($_SERVER);
// echo '</pre>';

// FORMULAIRE EN GET
// Les données vont dans l'URL (Uniform Resource Locator)
// Le problème pour un formulaire get est limité par 2000 caractères
// Cas d'usage => recherche ou filtres

// FORMULAIRE EN POST
// Les données vont dans le corps de la requête
// Données illimité
// Cas d'utilisation => connexion, formulaire, envoi de fichier

// On vérifie que c'est bien une requête en POST
// $_SERVER est une superglobale
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ici on continue le code car on a envoyé notre formulaire en post

    // on récupère les données et on les nettoie
    // Nouvelle superglobale => $_POST
    // C'est un tableau associatif qui contient toutes les données
    // de vos inputs.

    // echo '<pre>';
    // var_dump($_POST['birthdate']);
    // echo '</pre>';

    // On enlève les potentiels espaces en trop
    // mais pour éviter les null, on utilisera un operateur de coaléscence des nulls
    $firstname = trim($_POST['firstname'] ?? ''); // ?? => si c'est firstname est null alors ''
    $lastname = trim($_POST['lastname'] ?? '');
    $birthdate = $_POST['birthdate'] ?? '';
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $email_confirm = trim($_POST['email_confirm'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    $rue = trim($_POST['rue'] ?? '');
    $cp = trim($_POST['cp'] ?? '');
    $ville = trim($_POST['ville'] ?? '');
    $demande = trim($_POST['demande'] ?? '');

    // On vérifie si les valeurs sont vides
    // Si au moins une est vide, on retourne au formulaire
    // avec un message d'erreur
    if (
        empty($firstname)
        || empty($lastname)
        || empty($phone)
        || empty($email)
        || empty($email_confirm)
        || empty($password)
        || empty($password_confirm)
        || empty($rue)
        || empty($cp)
        || empty($ville)
        || empty($demande)
    ) {
        // Je retourne dans le formulaire, car une donnée est "vide".
        // De plus, je vais rajouter une erreur en GET
        header('Location: ../../views/auth/add_client_form.php?status=danger&message=Tous les champs obligatoires doivent être remplis.');
        exit;
    }

    // Vérification email = confirmation
    if ($email !== $email_confirm) {
        header('Location: ../../views/auth/add_client_form.php?status=danger&message=Les adresses e-mail ne correspondent pas.');
        exit;
    }

    // Vérification mot de passe = confirmation
    if ($password !== $password_confirm) {
        header('Location: ../../views/auth/add_client_form.php?status=danger&message=Les mots de passe ne correspondent pas.');
        exit;
    }

    // Vérification taille du mot de passe
    if (strlen($password) < 6) {
        header('Location: ../../views/auth/add_client_form.php?status=danger&message=Le mot de passe doit contenir au moins 6 caractères.');
        exit;
    }

    // Hashage du mot de passe
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // On a mettre les données dans la base de données.
    // APPEL DE PDO
    try {
        $pdo = getPDO();
        // on écrit la requête sql dans une variable
        $sql = 'INSERT INTO gestion_client (
                    firstname, lastname, birthdate, phone, email,
                    rue, cp, ville, demande, password
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        // On prépare la requête SQL
        $stmt = $pdo->prepare($sql);
        // On envoie les données dans la requête, qui remplace les points d'interrogations
        // et ensuite on execute
        // DANS UN EXECUTE, ON MET TOUJOURS UN TABLEAU, MÊME S'IL Y A QU'UNE SEULE DONNÉE
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
            $passwordHash,
        ]);

        header('Location: ../../views/auth/add_client_form.php?status=success&message=Inscription réussie. Vous pouvez maintenant vous connecter.');
        exit;

    } catch (PDOException $e) {
        // Ici on attrape l'erreur pour pouvoir l'envoyer dans le système de message qui se trouve dans le formulaire
        $error = $e->getMessage();
        header("Location: ../../views/auth/add_client_form.php?status=danger&message=$error");
        exit;
    }

} else {
    // On est éjecté du script si on accède directement par la page
    header('Location: ../../index.php');
    exit;
}
