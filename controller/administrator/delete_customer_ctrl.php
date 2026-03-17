<?php

session_start();
//empeche de supprimer un client s'il n'est pas admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'administrateur') {
    header("Location: ../../index.php");
    exit;
}

require_once __DIR__ . '/../../config.php';

// Vérifie que la requête est en POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_client = $_POST['id_client'] ?? null;

    if (!$id_client) {
        header("Location: ../../views/administrator/customer.php?status=danger&message=Client introuvable");
        exit;
    }

    try {

        $pdo = getPDO();

        $sql = "DELETE FROM gestion_client WHERE id_client = ?";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([$id_client]);

        header("Location: ../../views/administrator/customer.php?status=success&message=Le client a bien été supprimé avec succès");

        exit;

    } catch (PDOException $e) {

        $error = $e->getMessage();

        header("Location: ../../views/administrator/customer.php?status=danger&message=$error");

        exit;

    }

} else {

    header("Location: ../../index.php");
    exit;

}