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
$id_client  = $_POST['id_client'] ?? null;
$firstname  = trim($_POST['firstname'] ?? '');
$lastname   = trim($_POST['lastname'] ?? '');
$email      = trim($_POST['email'] ?? '');
$phone      = trim($_POST['phone'] ?? '');
$rue        = trim($_POST['rue'] ?? '');
$cp         = trim($_POST['cp'] ?? '');
$ville      = trim($_POST['ville'] ?? '');

if (!$id_client || !$firstname || !$lastname || !$email) {
    header("Location: ../../views/administrator/customer.php?status=danger&message=Veuillez remplir tous les champs obligatoires");
    exit;
}

try {
    $pdo = getPDO();

    // -------------------------
    // Mettre à jour le client
    // -------------------------
    $stmt = $pdo->prepare("
        UPDATE gestion_client
        SET firstname = :firstname,
            lastname  = :lastname,
            email     = :email,
            phone     = :phone,
            rue       = :rue,
            cp        = :cp,
            ville     = :ville
        WHERE id_client = :id_client
    ");

    $stmt->execute([
        'firstname' => $firstname,
        'lastname'  => $lastname,
        'email'     => $email,
        'phone'     => $phone,
        'rue'       => $rue,
        'cp'        => $cp,
        'ville'     => $ville,
        'id_client' => $id_client
    ]);

    header("Location: ../../views/administrator/customer.php?status=success&message=Le client a bien été mis à jour.");
    exit;

} catch (PDOException $e) {
    $error = $e->getMessage();
    header("Location: ../../views/administrator/customer.php?status=danger&message=$error");
    exit;
}
?>