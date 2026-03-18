<?php

// Démarrer la session seulement si elle n'existe pas déjà
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Connexion à la BDD
require_once __DIR__ . '/../../config.php';
$pdo = getPDO();

// ----------------- GESTION DES ACTIONS -----------------
$action = $_GET['action'] ?? '';

switch ($action) {

    // Marquer un message comme lu (sans redirection)
    case 'read':
        if (isset($_GET['id'])) {
            $id = (int) $_GET['id'];

            // Vérifier que le message existe
            $stmtCheck = $pdo->prepare("SELECT * FROM gestion_client WHERE id_client = ?");
            $stmtCheck->execute([$id]);
            $msg = $stmtCheck->fetch(PDO::FETCH_ASSOC);

            if ($msg && $msg['is_read'] == 0) {
                // Marquer comme lu
                $stmt = $pdo->prepare("UPDATE gestion_client SET is_read = 1 WHERE id_client = ?");
                $stmt->execute([$id]);
            }
            // NE PAS rediriger ici pour que view_messenger.php s'affiche correctement
        }
        break;

    // Supprimer un message
    case 'delete':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_client'])) {
            $id = (int) $_POST['id_client'];
            $stmt = $pdo->prepare("DELETE FROM gestion_client WHERE id_client = ?");
            $stmt->execute([$id]);
        }
        header("Location: ../../views/administrator/settings/messenger_customer.php?status=success&message=Le message a bien été supprimé avec succès");
        exit;
}

// ----------------- RÉCUPÉRATION DE TOUS LES MESSAGES -----------------
// ---------------- PAGINATION ----------------
$currentPage = max(1, (int)($_GET['page'] ?? 1));
$perPage = 10;
$offset = ($currentPage - 1) * $perPage;

// Compter le nombre total de messages
$totalMessages = (int)$pdo->query("SELECT COUNT(*) FROM gestion_client")->fetchColumn();
$totalPages = ceil($totalMessages / $perPage);

// Récupérer les messages pour la page
$stmt = $pdo->prepare("
    SELECT * FROM gestion_client
    ORDER BY id_client DESC
    LIMIT :limit OFFSET :offset
");

$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$all_messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ----------------- RÉCUPÉRATION D'UN MESSAGE UNIQUE (pour view_messenger.php) -----------------
$message = null;
if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    $stmt = $pdo->prepare("SELECT * FROM gestion_client WHERE id_client = ?");
    $stmt->execute([$id]);
    $message = $stmt->fetch(PDO::FETCH_ASSOC);
}