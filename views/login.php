<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../head.php';    // head_with_title

// On récupère le type d'utilisateur depuis l'URL
$type = $_GET['type'] ?? 'client';
$title = "Se connecter";


$content = <<<HTML
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <h1 class="h3 mb-3 text-center text-gris-fonce">Se connecter</h1>

            <form action="/projet/controller/auth/login_ctrl.php" method="POST">
                <div class="mb-3">
                    <label for="mail" class="form-label" style="color:#e38f3c;">Login (e-mail) :</label>
                    <input type="email" name="mail" id="mail" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="psw" class="form-label" style="color:#e38f3c;">Mot de passe :</label>
                    <input type="password" name="psw" id="psw" class="form-control" required>
                </div>

                <input type="hidden" name="type" value="<?= htmlspecialchars($type) ?>">

                <div class="text-center">
                    <button type="submit" class="btn w-50 text-white"> Connexion  </button>
                </div>
                <div class="text-center p-2">
                    <a class="text-gris-fonce" href="views/auth/add_client_form.php">Inscription</a>
                </div>
            </form>

        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
HTML;

echo password_hash("Garvitch_55100", PASSWORD_DEFAULT);

// ----------------- INCLURE LAYOUT -----------------
require __DIR__ . '/../layout.php';
