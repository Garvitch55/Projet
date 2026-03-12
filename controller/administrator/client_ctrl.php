<?php
// Démarrer la session seulement si elle n'existe pas déjà
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config.php';

// Définir le tableau des clients par défaut
$all_clients = [];
$error = '';

try {
    $pdo = getPDO();

    // Récupère la lettre sélectionnée dans l'URL, par défaut ALL
    $letter = $_GET['letter'] ?? 'ALL';

    // Pagination : récupérer la page actuelle (par défaut 1)
    $currentPage = max(1, (int)($_GET['page'] ?? 1));
    $perPage = 10; // 10 clients par page
    $offset = ($currentPage - 1) * $perPage;

    // Préparer la requête pour filtrer par nom de famille uniquement
    if ($letter !== 'ALL') {
        // Comptage total des clients pour cette lettre
        $countSql = "SELECT COUNT(*) FROM gestion_client WHERE lastname LIKE :letter";
        $countStmt = $pdo->prepare($countSql);
        $countStmt->execute(['letter' => $letter . '%']);
        $totalClients = (int)$countStmt->fetchColumn();

        // Récupération des clients pour cette page
        $sql = "SELECT * FROM gestion_client 
                WHERE lastname LIKE :letter
                ORDER BY lastname ASC
                LIMIT :limit OFFSET :offset";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':letter', $letter . '%', PDO::PARAM_STR);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
    } else {
        // Comptage total des clients
        $totalClients = (int)$pdo->query("SELECT COUNT(*) FROM gestion_client")->fetchColumn();

        // Récupération de tous les clients pour la page actuelle
        $sql = "SELECT * FROM gestion_client 
                ORDER BY lastname ASC
                LIMIT :limit OFFSET :offset";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
    }

    $all_clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calcul du nombre total de pages
    $totalPages = ceil($totalClients / $perPage);

} catch (PDOException $e) {
    $error = $e->getMessage();
    $all_clients = [];
    $totalPages = 1;
}