<?php


include_once '../../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $firstname = trim($_POST['firstname'] ?? '');
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
