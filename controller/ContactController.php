<?php
// controller/ContactController.php

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
$honeypot   = $_POST['website'] ?? '';

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

// ----------------- CONNEXION BDD -----------------
try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=gestion_entreprise;charset=utf8mb4",
        "root",
        ""
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    header("Location: ../views/contact.php?status=danger&message=" . urlencode("Erreur de connexion à la base de données."));
    exit;
}

// ----------------- INSERTION EN BDD -----------------
// on initialise is_read = 0 pour marquer le message comme non lu
$sql = "INSERT INTO contact (first_name, last_name, email, phone, subject, message, is_read) 
        VALUES (?, ?, ?, ?, ?, ?, 0)";
$stmt = $pdo->prepare($sql);

if ($stmt->execute([
    $first_name,
    $last_name,
    $email,
    $phone,
    $subject,
    $message
])) {
    header("Location: ../views/contact.php?status=success&message=" . urlencode("Nous avons bien reçu votre message et nous vous recontacterons dans les meilleurs délais."));
    exit;
} else {
    header("Location: ../views/contact.php?status=danger&message=" . urlencode("Erreur lors de l'envoi du message."));
    exit;
}

// ----------------- Récupération des derniers messages (pour layout) -----------------
// pour éviter le warning Undefined array key "is_read"
$sql2 = "SELECT id_contact, first_name, last_name, subject, created_at, is_read 
         FROM contact ORDER BY created_at DESC LIMIT 5";
$stmt2 = $pdo->query("SELECT first_name, last_name, subject, created_at, is_read FROM contact ORDER BY created_at DESC LIMIT 5");
$latest_messages = $stmt2->fetchAll(PDO::FETCH_ASSOC);
