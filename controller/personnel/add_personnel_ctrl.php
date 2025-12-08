<?php

include_once '../../config.php';
requireLogin();

// On ne pourra accéder à ce fichier uniquement par formulaire, sinon on est exclu
// est on retourne à l'index
if($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../../index.php");
    exit;
}

// On récupère et on nettoie les données.

$firstname = trim($_POST["firstname"]) ?? '';
$lastname = trim($_POST["lastname"]) ?? '';
$mail = trim($_POST['mail'] ?? '');
$psw = trim($_POST['psw'] ?? '');
$psw_confirmation = trim($_POST['psw_confirmation'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$birthdate = $_POST['birthdate'] ?? '';
$city = trim($_POST['city'] ?? '');

// On vérifie les valeurs obligatoires
if (
    empty($firstname) ||
    empty($lastname) ||
    empty($mail) ||
    empty($psw) ||
    empty($psw_confirmation)
) {
    header("Location: ../../views/staffs/add_staffs_form.php?status=danger&message=Le formulaire est mal remplie.");
    exit;
}

// MOT DE PASSE
if($psw === $psw_confirmation) {
    // Les mots de passe correspondent
    // On va pouvoir alors hasher le mot de passe
    // Nous utliserons l'algorithme argon2i
    // NE METTEZ JAMAIS LES MOTS DE PASSE EN CLAIR DANS UNE BASE DE DONNÉES
    
    if(strlen($psw) >= 8) {
        $psw = password_hash($psw, PASSWORD_ARGON2I);
    } else {
        header("Location: ../../views/staffs/add_staffs_form.php?status=danger&message=Le mot de passe est trop court.");
        exit;
    }
}else{
    header("Location: ../../views/staffs/add_staffs_form.php?status=danger&message=Les mots de passe ne correspondent pas.");
    exit;
}

// TÉLÉPHONE
// Regex du téléphone au format français
$phone_regex = '/^(0[1-9]\d{8}|\+33[1-9]\d{8})$/';
if(!empty($phone) && !preg_match($phone_regex, $phone)){
    header("Location: ../../views/staffs/add_staffs_form.php?status=danger&message=Le format du téléphone n'est pas valide.");
    exit;
}

// EMAIL
// sans regex
// Avec cette fonction, la vérification de l'email est complète et suit
// les normes RFC 5322 grammer.
if(!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
    header("Location: ../../views/staffs/add_staffs_form.php?status=danger&message=Le format du mail n'est pas valide.");
    exit;
}
//$mail_regex = '/^[a-zA-Z0-9.-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';

// Validation de la date de naissance
// On met la date de naissance du personnel en format utilisable (format US)
$date = DateTime::createFromFormat('Y-m-d', $birthdate);
$now = new DateTime();
$age = $now->diff($date)->y;

if($age < 18) {
    header("Location: ../../views/staffs/add_staffs_form.php?status=danger&message=Vous n'avez pas l'age nécessaire pour vous inscrire.");
    exit;
}

try {
//  on appelle PDO
    $pdo = getPDO();
    $sql = "INSERT INTO staff(firstname, lastname, mail, psw, birthdate, phone, city, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $firstname,
        $lastname,
        $mail,
        $psw,
        $birthdate,
        $phone,
        $city,
        null
    ]);
    header("Location: ../../views/staffs/add_staffs_form.php?status=success&message=Enregistrement réussi.");
    exit;

} catch (PDOException $e) {
    error_log("Erreur d'insertion : ". $e->getMessage());
    header("Location: ../../views/staffs/add_staffs_form.php?status=danger&message=Erreur de base de données.");
    exit;
}

