<?php
// controller/ContactController.php

require_once __DIR__ . '/../config.php';

// ----------------- Interdire accès direct -----------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../views/contact.php?status=danger&message=" . urlencode("Accès direct interdit."));
    exit;
}

// ----------------- Récupérer les données du formulaire -----------------
$first_name = trim($_POST['prenom'] ?? '');
$last_name  = trim($_POST['nom'] ?? '');
$email      = trim($_POST['email'] ?? '');
$phone      = trim($_POST['phone'] ?? '');
$subject    = trim($_POST['subject'] ?? '');
$message    = trim($_POST['message'] ?? '');
$honeypot   = $_POST['website'] ?? ''; // champ anti-spam invisible

// ----------------- ANTI-SPAM -----------------
if (!empty($honeypot)) {
    header("Location: ../views/contact.php?status=danger&message=" . urlencode("Spam détecté."));
    exit;
}

// ----------------- VALIDATION -----------------
if (empty($first_name) || empty($last_name) || empty($email) || empty($phone) || empty($subject) || empty($message)) {
    header("Location: ../views/contact.php?status=danger&message=" . urlencode("Veuillez remplir tous les champs."));
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: ../views/contact.php?status=danger&message=" . urlencode("Adresse email invalide."));
    exit;
}

// ----------------- INSERTION EN BDD -----------------
try {
    $pdo = getPDO(); // utilisation de la fonction de config.php

    $sql = "INSERT INTO contact (first_name, last_name, email, phone, subject, message, is_read) 
            VALUES (:first_name, :last_name, :email, :phone, :subject, :message, 0)";
    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':first_name' => $first_name,
        ':last_name'  => $last_name,
        ':email'      => $email,
        ':phone'      => $phone,
        ':subject'    => $subject,
        ':message'    => $message
    ]);

    // Redirection après succès
    header("Location: ../views/contact.php?status=success&message=" . urlencode("Nous avons bien reçu votre message et nous vous recontacterons dans les meilleurs délais."));
    exit;

} catch (PDOException $e) {
    // Redirection en cas d'erreur
    header("Location: ../views/contact.php?status=danger&message=" . urlencode("Erreur lors de l'envoi du message : " . $e->getMessage()));
    exit;
}

// ----------------- Récupération des derniers messages (pour layout, optionnel) -----------------
try {
    $stmt2 = $pdo->query("SELECT id_contact, first_name, last_name, subject, created_at, is_read 
                          FROM contact ORDER BY created_at DESC LIMIT 5");
    $latest_messages = $stmt2->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $latest_messages = []; // éviter warning
}