<?php

//empeche de supprimer un client s'il n'est pas admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'administrateur') {
    header("Location: ../../index.php");
    exit;
}

require_once __DIR__ . '/../../config.php';


$status = $message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $firstname = trim($_POST['firstname'] ?? '');
    $lastname = trim($_POST['lastname'] ?? '');
    $birthdate = $_POST['birthdate'] ?? null;
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $email_confirm = trim($_POST['email_confirm'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    $rue = trim($_POST['rue'] ?? '');
    $cp = trim($_POST['cp'] ?? '');
    $ville = trim($_POST['ville'] ?? '');
    $demande = trim($_POST['demande'] ?? '');

    // Vérification des champs obligatoires
      if (!$firstname || !$lastname || !$phone || !$email || !$email_confirm || !$password || !$password_confirm || !$rue || !$cp || !$ville || !$demande) {
        $message = urlencode("Tous les champs obligatoires doivent être remplis");
        header("Location: ../../administrator/settings/create_client.php?status=danger&message=$message");
        exit;
    }

    if ($email !== $email_confirm) {
        $message = urlencode("Les emails ne correspondent pas");
        header("Location: ../../administrator/settings/create_client.php?status=danger&message=$message");
        exit;
    }

    if ($password !== $password_confirm) {
        $message = urlencode("Les mots de passe ne correspondent pas");
        header("Location: ../../administrator/settings/create_client.php?status=danger&message=$message");
        exit;
    }

    if (strlen($password) < 6) {
        $message = urlencode("Le mot de passe doit contenir au moins 6 caractères");
        header("Location: ../../administrator/settings/create_client.php?status=danger&message=$message");
        exit;
    }

    // Hashage du mot de passe
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Insertion en base
    try {
        $pdo = getPDO();

        $sql = "INSERT INTO gestion_client (firstname, lastname, birthdate, phone, email, rue, cp, ville, demande, password) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $pdo->prepare($sql);
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
            $passwordHash
        ]);

          $message = urlencode("Le client a bien été créé avec succès");

        header("Location: ../../administrator/settings/create_client.php?status=success&message=$message");
        exit;

    } catch (PDOException $e) {

        $message = urlencode("Erreur : " . $e->getMessage());

        header("Location: ../../administrator/settings/create_client.php?status=danger&message=$message");
        exit;
    }
}