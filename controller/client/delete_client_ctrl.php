<?php

require_once __DIR__ . '/../../config.php';
// requireLogin();

//Pour éviter la menace CSRF (Cross-Site Request Forgery)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../views/children/children_list.php?status=danger&message=Tu n'as rien à faire là.");
    exit;
}

// Lecture du JSON envoyé par FETCH

// ancienne méthode
// $data = json_decode(file_get_contents("php://input"), true);

$data = json_decode(file_get_contents('php://input'), true);

$id_child = $_POST['id_child'] ?? null ;
$csrfToken = $_POST['csrf_token'] ?? null;

// PHP considère false une variable nulle
// On appelle ça les "falsy" type (undefined, null, 0, false, "")
if (!$id_child) {
    header('Location: ../../views/children/children_list.php?status=danger&message=ID introuvable');
    exit;
}

if (!$csrfToken && $csrfToken === $_SESSION['csrf_token']) {
    header('Location: ../../views/children/children_list.php?status=danger&message=Token non valide.');
    exit;
}

try {
    $pdo = getPDO();
    $sql = "UPDATE child SET is_delete = '1' WHERE id_child = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_child]);
    header('Location: ../../views/children/children_list.php?status=success&message=Suppréssion réussie.');
    exit;

} catch (PDOException $e) {
    header('Location: ../../views/children/children_list.php?status=danger&message=' . $e->getMessage());
    exit;
}
