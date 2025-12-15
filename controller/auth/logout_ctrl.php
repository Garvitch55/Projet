<?php
session_start();

$_SESSION = [];

session_destroy();

// Facultatif : supprimer le cookie de session côté client
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Redirection vers la page homepage
header("Location: ../../views/homepage.php?status=success&message=Déconnexion réussie");
exit;