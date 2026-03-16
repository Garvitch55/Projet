<?php
require_once __DIR__ . '/../../config.php';

// Vérifier que l'ID du devis est fourni
$id_quote = $_GET['id_quote'] ?? null;
if (!$id_quote) {
    echo json_encode([]);
    exit;
}

$pdo = getPDO();

// Récupérer les lignes du devis avec nom de l'ouvrage et quantité
$stmt = $pdo->prepare("
    SELECT qi.work_id, qi.quantity
    FROM quote_items qi
    WHERE qi.quote_id = ?
");
$stmt->execute([$id_quote]);
$lines = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Retour JSON
header('Content-Type: application/json');
echo json_encode($lines);