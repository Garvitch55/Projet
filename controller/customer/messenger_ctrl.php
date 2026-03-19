<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config.php';
$pdo = getPDO();

// Sécurité
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'client') {
    header("Location: ../../login.php");
    exit;
}

$client_id = $_SESSION['id'];

// ----------------- ACTIONS -----------------
$action = $_GET['action'] ?? '';

switch ($action) {

    // Marquer comme lu
    case 'read':
        $stmt = $pdo->prepare("
            UPDATE gestion_client
            SET is_read = 1
            WHERE id_client = :id
        ");
        $stmt->execute(['id' => $client_id]);
        break;

    // Supprimer la demande
    case 'delete':
        $stmt = $pdo->prepare("
            UPDATE gestion_client
            SET demande = NULL
            WHERE id_client = :id
        ");
        $stmt->execute(['id' => $client_id]);

        header("Location: ../../views/customer/messenger.php?status=success&message=" . urlencode("Message supprimé"));
        exit;
}

// ----------------- PAGINATION -----------------
$currentPage = max(1, (int)($_GET['page'] ?? 1));
$perPage = 10;
$offset = ($currentPage - 1) * $perPage;

// Total
$stmtTotal = $pdo->prepare("
    SELECT COUNT(*) 
    FROM gestion_client
    WHERE id_client = :id AND demande IS NOT NULL
");
$stmtTotal->execute(['id' => $client_id]);

$totalMessages = (int)$stmtTotal->fetchColumn();
$totalPages = ceil($totalMessages / $perPage);

// Messages
$stmt = $pdo->prepare("
    SELECT demande, created_at, is_read
    FROM gestion_client
    WHERE id_client = :id AND demande IS NOT NULL
    ORDER BY created_at DESC
    LIMIT :limit OFFSET :offset
");

$stmt->bindValue(':id', $client_id, PDO::PARAM_INT);
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$all_messages = $stmt->fetchAll(PDO::FETCH_ASSOC);