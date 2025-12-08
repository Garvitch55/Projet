<?php

if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

/** 
 * Savoir si un utilisateur est connecté
 * @return bool
 */
function isUserLoggedIn(): bool {
    return isset($_SESSION['name']);
}

/**
 * Forcer la connexion pour une page : si non connecter -> redirection vers le login
 */
function requireLogin(): void {
    if(!isUserLoggedIn()) {
        header("Location: login.php?status=danger&message=Veillez vous connecter.");
    }
}

/**
 * Permet de faciliter l'écriture du nom et prénom d'une personne, à partir d'un tableau associatif
 * @param $assocArray
 */
function getFullName(array $assocArray): string {
    return $assocArray['firstname'] . " " . $assocArray['lastname'];
}

// On va définir l'adresse de la racine comme une variable global constante
define('ROOT', __DIR__. '/');

// Fonction qui charge le fichier

function load(?string $file) { //?string: string | null
    require ROOT . $file;
}

function start_page(string $title) {
    load('head.php');
    head_with_title($title);
    load('nav.php');
}

function getPDO() {
    $pdo = new PDO("mysql:host=db;dbname=gestion_client;chartset=utf8", "root", "1234");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
}

/**
 * Détermine si un nom ou un mot commance par une voyelle et renvoie une bonne chaine adaptée.
 * 
 * Cette fonction analyse la première lettre d'une chaine.
 * Véfirie si elle fait partie d'un ensemble de voyelles définies,
 * puis renvoie l'une des deux chaines passée en paramètres.
 * @param string $name Le mot ou le nom à analyser.
 * @param string $apo Chaine retourne si le nom commance par une consonne.
 * @param string $noApo Chaine retourne si le nom commance par une voyelle.
 * 
 * @return string Retourne si $apo ou $noApo selon la première lettre du nom.
 */
function firstLetterVowelDetector($name, $noApo, $apo) : string {
    $vowels = ["a", "e", "i", "o", "u", "é", "è"];

    //Ici, dans substr, on prend une string $name, on récupère à partir de l'index 0
    // et on prend 1 seule lettre.
    // et ensuite je le met en minuscule
    $firstLetter = mb_strtolower(mb_substr($name, 0, 1));

    //on vérifie si la première lettre est dans le tableau de voyelles
    if(in_array($firstLetter, $vowels)) {
        return $apo;
    } else {
        return $noApo;
    }

}