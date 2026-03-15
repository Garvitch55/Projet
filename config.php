<?php

// --------------------------
// SESSION
// --------------------------
// On ne démarre la session que si elle n'existe pas encore
// ET que l'on n'est pas en ligne de commande (CLI)
if (php_sapi_name() !== 'cli' && session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Savoir si un utilisateur est connecté
 * @return bool
 */
function isUserLoggedIn(): bool
{
    return isset($_SESSION['name']);
}

/**
 * Forcer la connexion pour une page : si non connecté -> redirection vers le login
 */
function requireLogin(): void
{
    if (!isUserLoggedIn()) {
        header('Location: login.php?status=danger&message=Veillez vous connecter.');
        exit;
    }
}

/**
 * Permet de faciliter l'écriture du nom et prénom d'une personne, à partir d'un tableau associatif
 * @param array $assocArray
 * @return string
 */
function getFullName(array $assocArray): string
{
    return $assocArray['firstname'] . ' ' . $assocArray['lastname'];
}

// On va définir l'adresse de la racine comme une variable globale constante
define('ROOT', __DIR__ . '/');

/**
 * Fonction qui charge un fichier relatif à la racine
 * @param string|null $file
 */
function load(?string $file)
{ 
    require ROOT . $file;
}

/**
 * Retourne un objet PDO connecté à la base de données
 * @return PDO
 */
function getPDO(): PDO
{
    $pdo = new PDO(
        'mysql:host=localhost;dbname=gestion_entreprise;charset=utf8mb4',
        'root',
        ''
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
}

/**
 * Récupère le rôle de l'utilisateur connecté
 * @return string|null
 */
function getUserRole(): ?string
{
    return $_SESSION['role'] ?? null; // "administrateur", "employe", "client"
}