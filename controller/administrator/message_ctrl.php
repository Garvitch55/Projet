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
            $stmtCheck = $pdo->prepare("SELECT * FROM contact WHERE id_contact = ?");
            $stmtCheck->execute([$id]);
            $msg = $stmtCheck->fetch(PDO::FETCH_ASSOC);

            if ($msg && $msg['is_read'] == 0) {
                // Marquer comme lu
                $stmt = $pdo->prepare("UPDATE contact SET is_read = 1 WHERE id_contact = ?");
                $stmt->execute([$id]);
            }
            // NE PAS rediriger ici pour que view_message.php s'affiche correctement
        }
        break;

    // Supprimer un message
    case 'delete':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_contact'])) {
            $id = (int) $_POST['id_contact'];
            $stmt = $pdo->prepare("DELETE FROM contact WHERE id_contact = ?");
            $stmt->execute([$id]);
        }
        header("Location: ../../views/administrator/settings/messages.php");
        exit;
}

// ----------------- RÉCUPÉRATION DE TOUS LES MESSAGES -----------------
// ---------------- PAGINATION ----------------
$currentPage = max(1, (int)($_GET['page'] ?? 1));
$perPage = 10;
$offset = ($currentPage - 1) * $perPage;

// Compter le nombre total de messages
$totalMessages = (int)$pdo->query("SELECT COUNT(*) FROM contact")->fetchColumn();
$totalPages = ceil($totalMessages / $perPage);

// Récupérer les messages pour la page
$stmt = $pdo->prepare("
    SELECT * FROM contact
    ORDER BY created_at DESC
    LIMIT :limit OFFSET :offset
");

$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$all_messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ----------------- RÉCUPÉRATION D'UN MESSAGE UNIQUE (pour view_message.php) -----------------
$message = null;
if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    $stmt = $pdo->prepare("SELECT * FROM contact WHERE id_contact = ?");
    $stmt->execute([$id]);
    $message = $stmt->fetch(PDO::FETCH_ASSOC);
}