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
// Récupération données
// -------------------------
$id_personnel = $_POST['id_personnel'] ?? null;
$firstname    = trim($_POST['firstname'] ?? '');
$lastname     = trim($_POST['lastname'] ?? '');
$mail         = trim($_POST['mail'] ?? '');
$phone        = trim($_POST['phone'] ?? '');
$fonction     = $_POST['fonction'] ?? '';

// -------------------------
// Vérification
// -------------------------
if (!$id_personnel || !$firstname || !$lastname || !$mail || !$fonction) {
    header("Location: ../../views/administrator/settings/list_staff.php?status=danger&message=Champs obligatoires manquants");
    exit;
}

try {
    $pdo = getPDO();

    // -------------------------
    // Update
    // -------------------------
    $stmt = $pdo->prepare("
        UPDATE gestion_personnel
        SET firstname = :firstname,
            lastname  = :lastname,
            mail      = :mail,
            phone     = :phone,
            fonction  = :fonction
        WHERE id_personnel = :id_personnel
    ");

    $stmt->execute([
        'firstname'    => $firstname,
        'lastname'     => $lastname,
        'mail'         => $mail,
        'phone'        => $phone,
        'fonction'     => $fonction,
        'id_personnel' => $id_personnel
    ]);

    header("Location: ../../views/administrator/settings/list_staff.php?status=success&message=Employé modifié avec succès");
    exit;

} catch (PDOException $e) {
    $error = $e->getMessage();
    header("Location: ../../views/administrator/settings/list_staff.php?status=danger&message=$error");
    exit;
}
?>