<?php

include_once '../../config.php';

// Vérifie que la requête est en POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['mail'] ?? '');
    $password = $_POST['psw'] ?? '';

    if (empty($email) || empty($password)) {
        header('Location: ../../views/login.php?status=danger&message=Veuillez remplir tous les champs.');
        exit;
    }

    try {
        $pdo = getPDO();

        // --------------------------------------------------------
        // 1️⃣ Vérification dans la table staff (admin/employé)
        // --------------------------------------------------------
        $stmt = $pdo->prepare('SELECT * FROM gestion_personnel WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            if (password_verify($password, $user['password'])) {

                $_SESSION['id'] = $user['id_personnel'];
                $_SESSION['name'] = $user['firstname'] . ' ' . $user['lastname'];
                $_SESSION['role'] = $user['fonction']; // admin ou employe

                // Redirection selon le rôle
                if ($_SESSION['role'] === 'administrateur') {
                    header('Location: ../../views/homepage.php?status=success&message=Bienvenue Administrateur ' . $_SESSION['name']);
                } else {
                    header('Location: ../../views/homepage.php?status=success&message=Bienvenue Employé ' . $_SESSION['name']);
                }
                exit;
            } else {
                header('Location: ../../views/login.php?status=danger&message=Mot de passe incorrect.');
                exit;
            }
        }

        // --------------------------------------------------------
        // 2️⃣ Vérification dans la table client
        // --------------------------------------------------------
        $stmt = $pdo->prepare('SELECT * FROM gestion_client WHERE email = ?');
        $stmt->execute([$email]);
        $client = $stmt->fetch();

        if ($client && password_verify($password, $client['password'])) {
            $_SESSION['id'] = $client['id_client'];
            $_SESSION['name'] = $client['firstname'] . ' ' . $client['lastname'];
            $_SESSION['role'] = 'client';

            header('Location: ../../index.php?status=success&message=Bienvenue, ' . $_SESSION['name']);
            exit;
        }

        // Aucun utilisateur trouvé
        header('Location: ../../views/login.php?status=danger&message=Utilisateur inconnu.');
        exit;
    } catch (PDOException $e) {
        $error = $e->getMessage();
        header("Location: ../../views/login.php?status=danger&message=$error");
        exit;
    }
} else {
    header('Location: ../../index.php');
    exit;
}
