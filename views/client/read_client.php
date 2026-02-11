<?php

if (!isset($_GET['id'])) {
    header('Location: children_list.php?page=1');
    exit;
}

$id = (int) $_GET['id'];
require_once __DIR__ . "/../../config.php";
requireLogin();

$pdo = getPDO();
$sql = "SELECT * FROM child WHERE id_child = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);

$child = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$child) {
    header('Location: children_list.php?page=1');
    exit;
}

start_page("Modifier un pensionnaire");
?>

<h1 class="text-center mt-3">Fiche <?= firstLetterVowelDetector($child["firstname"], "de ", "d'") . htmlentities($child["firstname"] . " " . $child["lastname"]) ?></h1>

