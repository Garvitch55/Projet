<?php

require_once __DIR__ . "/config.php";

start_page("Connexion");
?>

<?php

include __DIR__ . "/notification.php";
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <h1 class="h3 mb-3 text-center">Connexion</h1>

            <form action="controller/auth/login_ctrl.php" method="POST">
                <div class="mb-3">
                    <label for="mail" class="form-label">Login (e-mail) :</label>
                    <input type="email" name="mail" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="psw" class="form-label">Mot de passe :</label>
                    <input type="password" name="psw" class="form-control" required>
                </div>
                <div class="text-center">
                    <input type="submit" class="btn btn-primary w-50 text-center" value="Connexion">
                </div>
            </form>
        </div>
    </div>
</div>