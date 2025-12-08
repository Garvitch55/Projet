<?php

include_once "../../config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = trim($_POST['firstname'] ?? '');
    $lastname = trim($_POST['lastname'] ?? '');
    $birthdate = $_POST['birthdate'] ?? '';
    $biosex = $_POST['biosex'] ?? '';
    $origin = $_POST['origin'] ?? '';
    $description = trim($_POST['description'] ?? '');
    $id_child = $_POST['id_child'] ?? '';

    if (
        empty($firstname) || 
        empty($lastname) ||
        empty($birthdate) ||
        empty($biosex) ||
        empty($origin) ||
        empty($description) ||
        empty($id_child)
    ) {
        header("Location: ../../views/children/update_children_form.php?status=danger&message=Le formulaire est mal remplie.");
        exit;
    }

    $today = new DateTime();

    $birthdateObj = DateTime::createFromFormat('Y-m-d', $birthdate);

    $age = $today->diff($birthdateObj)->y;

    if($age >= 18) {
        header("Location: ../../views/children/update_children_form.php?status=danger&message=Pensionnaire trop agé(e).");
        exit;
    }

    // On a mettre les données dans la base de données.
    // APPEL DE PDO
    try {
        $pdo = getPDO();
        $sql = "UPDATE child SET firstname=?, lastname=?, birthdate=?, biosex=?, origin=?, description=? WHERE id_child=?";
        // Playsolder
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $firstname,
            $lastname, 
            $birthdate, 
            $biosex, 
            $origin, 
            $description,
            $id_child
        ]);

        header("Location: ../../views/children/update_children_form.php?status=success&message=Pensionnaire modifié avec succès");
    } catch (PDOException $e) {
        // Ici on attrape l'erreur pour pouvoir l'envoyer dans le système de message qui se trouve dans le formulaire
        $error = $e->getMessage();
        header("Location: ../../views/children/update_children_form.php?status=danger&message=$error");
        exit;
    }


} else {
    // On est éjecté du script si on accède directement par la page
    header("Location: ../../index.php");
    exit;
}