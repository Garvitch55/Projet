<?php
require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../../head.php';
// Inclure le controller pour traiter le formulaire
require __DIR__ . '/../../../controller/administrator/create_client_ctrl.php';

// Vérifie si admin
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'administrateur') {
    header("Location: ../../../../index.php?status=danger&message=Accès refusé.");
    exit;
}

$title = "Créer un client";



?>

<?php
ob_start();
require ROOT . "notification.php";
$notification = ob_get_clean();
?>

<section class="m-4">
    
    <div class="d-flex justify-content-between align-items-center mt-3">
        <h1 class="text-orange-fonce mb-4">Créer un nouveau client</h1>

        <a href="/projet/views/administrator/settings/list_clients.php" class="btn text-white">
            <i class="bi bi-arrow-left me-2"></i> Retour
        </a>
    </div>

    <form method="POST" action="" class="row g-3">

        <div class="col-md-6">
            <label class="form-label">Prénom : <span class="text-danger">*</span></label>
            <input type="text" name="firstname" class="form-control" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Nom: <span class="text-danger">*</span></label>
            <input type="text" name="lastname" class="form-control" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Date de naissance : <span class="text-danger">*</span></label>
            <input type="date" name="birthdate" class="form-control">
        </div>

        <div class="col-md-6">
            <label class="form-label">Téléphone : <span class="text-danger">*</span></label>
            <input type="text" name="phone" class="form-control" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Email : <span class="text-danger">*</span></label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Confirmer Email : <span class="text-danger">*</span></label>
            <input type="email" name="email_confirm" class="form-control" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Mot de passe : <span class="text-danger">*</span></label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Confirmer mot de passe : <span class="text-danger">*</span></label>
            <input type="password" name="password_confirm" class="form-control" required>
        </div>

        <div class="col-md-12">
            <label class="form-label">Rue : <span class="text-danger">*</span></label>
            <input type="text" name="rue" class="form-control" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Code postal : <span class="text-danger">*</span></label>
            <input type="text" name="cp" class="form-control" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Ville : <span class="text-danger">*</span></label>
            <input type="text" name="ville" class="form-control" required>
        </div>

        <div class="col-md-12">
            <label class="form-label">Demande du client : <span class="text-danger">*</span></label>
            <textarea name="demande" class="form-control" required></textarea>
        </div>

        <div class="col-12">
            <button type="submit" class="btn text-white">Créer le client</button>
        </div>

    </form>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<?php
$content = ob_get_clean();
require __DIR__ . '/../../../layout.php';