<?php

require_once __DIR__ . "/../../config.php";
// requireLogin();

//Pour éviter la menace CSRF (Cross-Site Request Forgery)
if($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode([
        "status" => "danger",
        "message" => "Tu n'as rien à faire là."
    ]);
    exit;
}

// Lecture du JSON envoyé par FETCH

// ancienne méthode
// $data = json_decode(file_get_contents("php://input"), true);

$data = json_decode(file_get_contents("php://input"), true);

$id_staff = $_POST["id_staff"] ?? null ;
$csrfToken = $_POST["csrf_token"] ?? null;

// PHP considère false une variable nulle
// On appelle ça les "falsy" type (undefined, null, 0, false, "")
if (!$id_staff) {
    echo json_encode([
        "status" => "danger",
        "message" => "ID nul"
    ]);
    exit;
}

if (!$csrfToken && $csrfToken === $_SESSION['csrf_token']){
    echo json_encode([
        "status" => "danger",
        "message" => "Token invalide."
    ]);
    exit;
}

try {
    $pdo = getPDO();
    $sql = "DELETE FROM staff WHERE id_staff = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_staff]);
    echo json_encode([
        "status" => "success",
        "message" => "Membre du personnel supprimé avec succès."
    ]);
    exit;

} catch (PDOException $e) {
    echo json_encode([
        "status" => "danger",
        "message" => "Erreur serveur: " . $e->getMessage()
    ]);
    exit;
}