<?php

require_once __DIR__ . '/../../config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

// Sécurité
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'client') {
    echo json_encode(['success' => false, 'error' => 'Non autorisé']);
    exit;
}

$pdo = getPDO();

$client_id = $_SESSION['id'];
$demande = trim($_POST['demande'] ?? '');

if (empty($demande)) {
    echo json_encode(['success' => false, 'error' => 'Message vide']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        UPDATE gestion_client
        SET demande = :demande,
            created_at = NOW(),
            is_read = 0
        WHERE id_client = :id_client
    ");

    $stmt->execute([
        'demande' => $demande,
        'id_client' => $client_id
    ]);

    echo json_encode([
        'success' => true,
        'demande' => htmlspecialchars($demande)
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}