<?php
session_start();

// -------------------------
// Vérification admin
// -------------------------
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'administrateur') {
    header("Location: ../../index.php");
    exit;
}

// -------------------------
// Vérification POST
// -------------------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../index.php");
    exit;
}

require_once __DIR__ . '/../../config.php';

// -------------------------
// Récupération des champs du formulaire
// -------------------------
$firstname  = trim($_POST['firstname'] ?? '');
$lastname   = trim($_POST['lastname'] ?? '');
$mail       = trim($_POST['mail'] ?? '');
$phone      = trim($_POST['phone'] ?? '');
$rue        = trim($_POST['rue'] ?? '');
$cp         = trim($_POST['cp'] ?? '');
$ville      = trim($_POST['ville'] ?? '');
$fonction   = $_POST['fonction'] ?? 'employe';
$password   = trim($_POST['password'] ?? 'Password123'); // mot de passe par défaut si vide

// -------------------------
// Vérification des champs obligatoires
// -------------------------
if (!$firstname || !$lastname || !$mail || !$fonction) {
    header("Location: ../../views/administrator/create_staff.php?status=danger&message=Veuillez remplir tous les champs obligatoires");
    exit;
}

// -------------------------
// Hash du mot de passe
// -------------------------
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

try {
    $pdo = getPDO();

    // -------------------------
    // Insertion du membre du personnel
    // -------------------------
    $stmt = $pdo->prepare("
        INSERT INTO gestion_personnel
        (firstname, lastname, mail, phone, rue, cp, ville, password, fonction)
        VALUES (:firstname, :lastname, :mail, :phone, :rue, :cp, :ville, :password, :fonction)
    ");

    $stmt->execute([
        'firstname' => $firstname,
        'lastname'  => $lastname,
        'mail'      => $mail,
        'phone'     => $phone,
        'rue'       => $rue,
        'cp'        => $cp,
        'ville'     => $ville,
        'password'  => $passwordHash,
        'fonction'  => $fonction
    ]);

    header("Location: ../../views/administrator/settings/list_staff.php?status=success&message=Le membre du personnel a bien été créé avec succès.");
    exit;

} catch (PDOException $e) {
    $error = $e->getMessage();
    header("Location: ../../views/administrator/settings/create_staff.php?status=danger&message=$error");
    exit;
}