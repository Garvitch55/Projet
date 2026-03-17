<?php
require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../../head.php';

$title = "Créer un membre du personnel";

ob_start();
?>

<section class="m-4">

    <div class="d-flex justify-content-between align-items-center mt-3 mb-4">
        <h1 class="text-orange-fonce">Créer un membre du personnel</h1>
        <a href="views/administrator/settings/list_staff.php" class="btn text-white">
            <i class="bi bi-arrow-left me-2"></i> Retour
        </a>
    </div>

    <form action="/projet/controller/administrator/create_staff_ctrl.php" method="POST">

        <!-- Prénom -->
        <div class="mb-3">
            <label for="firstname" class="form-label">Prénom: <span class="text-danger">*</span></label>
            <input type="text" name="firstname" id="firstname" class="form-control" required>
        </div>

        <!-- Nom -->
        <div class="mb-3">
            <label for="lastname" class="form-label">Nom: <span class="text-danger">*</span></label>
            <input type="text" name="lastname" id="lastname" class="form-control" required>
        </div>

        <!-- Email -->
        <div class="mb-3">
            <label for="mail" class="form-label">Email: <span class="text-danger">*</span></label>
            <input type="email" name="mail" id="mail" class="form-control" required>
        </div>

        <!-- Téléphone -->
        <div class="mb-3">
            <label for="phone" class="form-label">Téléphone:</label>
            <input type="text" name="phone" id="phone" class="form-control">
        </div>

        <!-- Adresse -->
        <div class="mb-3">
            <label for="rue" class="form-label">Rue:</label>
            <input type="text" name="rue" id="rue" class="form-control">
        </div>

        <div class="mb-3">
            <label for="cp" class="form-label">Code postal:</label>
            <input type="text" name="cp" id="cp" class="form-control">
        </div>

        <div class="mb-3">
            <label for="ville" class="form-label">Ville:</label>
            <input type="text" name="ville" id="ville" class="form-control">
        </div>

        <!-- Mot de passe -->
        <div class="mb-3">
            <label for="password" class="form-label">Mot de passe: <span class="text-danger">*</span></label>
            <input type="text" name="password" id="password" class="form-control" placeholder="Mot de passe par défaut si vide" required>
        </div>

        <!-- Rôle -->
        <div class="mb-3">
            <label for="fonction" class="form-label">Rôle: <span class="text-danger">*</span></label>
            <select name="fonction" id="fonction" class="form-select" required>
                <option value="employe">Employé</option>
                <option value="administrateur">Administrateur</option>
            </select>
        </div>

        <button type="submit" class="btn text-white">Créer</button>
    </form>

</section>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../../layout.php';
?>