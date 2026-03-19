<?php
ini_set('display_errors', 0); // Ne pas afficher d'erreurs à l'écran
error_reporting(E_ALL);
require_once __DIR__ . '/../../config.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'administrateur') {
    echo json_encode(['success' => false, 'error' => 'Accès refusé']);
    exit;
}

$pdo = getPDO();

$client_id = $_POST['id_client'] ?? null;
$message = trim($_POST['reply'] ?? '');

if (!$client_id || !$message) {
    echo json_encode(['success' => false, 'error' => 'Données manquantes']);
    exit;
}

try {
    // Insérer la réponse
$stmt = $pdo->prepare("
    INSERT INTO client_replies (client_id, reply_personnel, personnel_id, created_at, response_date)
    VALUES (?, ?, ?, NOW(), NOW())
");
$stmt->execute([$client_id, $message, $_SESSION['id']]);

    $personnel_name = $_SESSION['name'];
    $created_at = date('d/m/Y H:i');

    echo json_encode([
        'success' => true,
        'message' => nl2br(htmlentities($message)),
        'personnel_name' => htmlentities($personnel_name),
        'created_at' => $created_at
    ]);
} catch (\PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
exit;