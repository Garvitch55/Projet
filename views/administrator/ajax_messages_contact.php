<?php
require_once __DIR__ . '/../../config.php';

if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'administrateur') {
    http_response_code(403);
    exit('Accès refusé');
}

$pdo = getPDO();

$stmt = $pdo->prepare("
    SELECT id_contact, first_name, last_name, subject, created_at
    FROM contact
    WHERE is_read = 0
    ORDER BY created_at DESC
    LIMIT 5
");
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($messages) {
    echo '<ul class="list-group list-group-flush rounded-1 border-0" style="max-height:400px; overflow-y:auto;">';
    foreach ($messages as $msg) {
        echo '<li class="list-group-item d-flex justify-content-between align-items-center p-2 border border-white">
                <div>
                    <strong>'.htmlentities($msg['first_name'].' '.$msg['last_name']).'</strong><br>
                    '.htmlentities($msg['subject']).'<br>
                    <small>'.htmlentities($msg['created_at']).'</small>
                </div>
                <div>
                    <a href="views/administrator/settings/view_messenger_contact.php?id='.$msg['id_contact'].'&action=read"
                       class="btn3 btn-sm text-white view-message"
                       style="width:35px; height:35px;"
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