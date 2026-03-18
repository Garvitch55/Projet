<?php
require_once __DIR__ . '/../../config.php';

if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'administrateur') {
    http_response_code(403);
    exit('Accès refusé');
}

$pdo = getPDO();

// On sélectionne les clients dont le message n'a pas été lu (is_read = 0)
// Tri par id_client décroissant pour afficher les plus récents
$stmt = $pdo->prepare("
    SELECT id_client, firstname, lastname, demande, created_at
    FROM gestion_client
    WHERE is_read = 0
    ORDER BY id_client DESC
");
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($messages) {
    echo '<ul class="list-group list-group-flush rounded-1 border-0" style="max-height:400px; overflow-y:auto;">';
    foreach ($messages as $msg) {
echo '<li class="list-group-item d-flex justify-content-between align-items-center rounded-1 border border-white"
    style="width: 97%; margin: auto; transition: transform 0.2s; padding: 0.75rem 1rem; border-radius: 0;">
    <div>
        <div class="fw-bold">'
        .htmlentities($msg['firstname'].' '.$msg['lastname']).'</div>
        <div class="small">'
        .htmlentities($msg['demande']).'
        </div>
        <div class="small text-white">'
        .date('d/m/Y H:i', strtotime($msg['created_at'])).'
        </div>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="views/administrator/settings/view_messenger_customer.php?id='.$msg['id_client'].'&action=read"
           class="btn3 btn-sm d-flex justify-content-center align-items-center text-white rounded-1 view-message"
           style="width:40px; height:40px;"
           title="Lire le message">
            <i class="fa-solid fa-envelope-open"></i>
        </a>
    </div>
</li>';
    }
    echo '</ul>';
} else {
    echo '<p>Aucun message non lu</p>';
}