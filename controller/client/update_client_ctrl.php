<?php

// L'inport du PHP Data Object
// PDO sécurise la plus grosse faille d'internet : L'injection SQL
include_once '../../config.php';
requireLogin();
// Fichier qui ajoute les enfants dans la base de données

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

//On vérifie que c'est bien une requête en POST
// $_SERVER est une superglobale

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ici on continue le code car on a envoyer notre formulaire en post

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
    // Ici si $_POST['firstname'] est null alors il deviendra '' (une chaine vide)
    // si il n'est pas null il gardera sa valeur, par exemple 'Jean-Michel'
    $lastname = trim($_POST['lastname'] ?? '');
    $birthdate = $_POST['birthdate'] ?? '';
    $biosex = $_POST['biosex'] ?? '';
    $origin = $_POST['origin'] ?? '';
    $description = trim($_POST['description'] ?? '');
    $id_child = $_POST['id_child'] ?? '';

    // On vérifie si les valeurs sont vides
    // Si au moins une est vide, on retourne au formulaire
    // avec un message d'erreur

    if (
        empty($firstname)
        || empty($lastname)
        || empty($birthdate)
        || empty($biosex)
        || empty($origin)
        || empty($description)
        || empty($id_child)
    ) {
        // Je retourne dans le formulaire, car une données est "vide".
        // De plus, je vais rajouter une erreur en GET
        header("Location: ../../views/children/update_children_form.php?id=$id_child&status=danger&message=Le formulaire est mal remplie.");
        exit;
    }

    // Vérification que l'âge des enfants enregistrer -18 ans
    // On a besoin de la date d'aujourd'hui
    // pour pouvoir comparer la date de naissance
    $today = new DateTime();
    // Ensuite on a besoin de la date de naissance du mioche
    $birthdateObj = DateTime::createFromFormat('Y-m-d', $birthdate);

    // On va calculer la différence.
    // Cependant par défaut il ne sait pas ce qu'on veut. On veut le nombres d'années 'y'
    $age = $today->diff($birthdateObj)->y;

    if ($age >= 18) {
        header("Location: ../../views/children/update_children_form.php?id=$id_child&status=danger&message=Pensionnaire trop agé(e).");
        exit;
    }

    // On a mettre les données dans la base de données.
    // APPEL DE PDO
    try {
        $pdo = getPDO();
        // on écrit le requête sql une variable
        $sql = 'UPDATE child SET firstname=?, lastname=?, birthdate=?, biosex=?, origin=?, description=? WHERE id_child=?';
        // On prépare le requête SQL
        $stmt = $pdo->prepare($sql);
        // On envoi les données dans la requête, qui remplace les points d'interrogations)
        // et ensuite on execute
        // DANS UN EXECUTE, ON MET TOUJOURS UN TABLEAU, MÊME S'IL Y A QU'UNE SEULE DONNÉE
        $stmt->execute([
            $firstname,
            $lastname,
            $birthdate,
            $biosex,
            $origin,
            $description,
            $id_child,
        ]);

        header("Location: ../../views/children/update_children_form.php?id=$id_child&status=success&message=Pensionnaire modifié avec succès");
    } catch (PDOException $e) {
        // Ici on attrape l'erreur pour pouvoir l'envoyer dans le système de message qui se trouve dans le formulaire
        $error = $e->getMessage();
        header("Location: ../../views/children/update_children_form.php?id=$id_child&status=danger&message=$error");
        exit;
    }


} else {
    // On est éjecté du script si on accède directement par la page
    header('Location: ../../index.php');
    exit;
}
