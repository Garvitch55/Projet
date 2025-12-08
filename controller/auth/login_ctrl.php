<?php

require_once __DIR__ . "/../../config.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../../index.php");
    exit;
}

$email = trim($_POST['mail'] ?? '');
$password = $_POST['psw'] ?? '';

if (empty($email) || empty($password)) {
    header("Location: ./login.php?status=danger?message=Veuillez remplir tous les champs.");
    exit;
}

try {
    // on récupère l'utilisateur par son mail
    $pdo = getPDO();

    $sql = "SELECT id_gestion, mail, psw, firstname, lastname, role 
    FROM staff WHERE mail = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    // si le mail est bon, staff est la parsonne qu'on recherche avec le mail
    // sinon il retourne un booléen false
    $staff = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$staff) {
        header("Location: ../../login.php?status=danger&message=Email ou mot de passe incorrect.");
    }

    // ----- On vérifie le mot de passe -----
    if(!password_verify($password, $staff['psw'])) {
        header("Location: ../../login.php?status=danger&message=Mot de passe incorrect.");
        exit; // Une fois hashé, on ne plus le rendre non hashé
        // hashage =/= cryptage
    }

    // ----- Ouverture de session -----
    // Pour que le système de session marche, il nous faut une session_start()
    // Il nous faut uniquement un par page, sinon erreur, si zero, le système de session ne marche pas
    // $_SESSION est du BACK-END !
    $_SESSION["name"] = $staff["firstname"] . " " . $staff["lastname"];
    $_SESSION["role"] = $staff["role"];
    // On va générer un token CSRF (Cross Site Request Forgery)
    // Ici on génère un chiffre en binaire avec 32 0 ou 1
    // Après on le convertit en hexadécimal.
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    header("Location: ../../index.php");
    exit;

} catch(PDOException $e) {
    $message = $e->getMessage();
    header("Location: ./login.php?status=danger&message=$message");
}