<?php
require_once __DIR__ . "/../config.php";
start_page("Connexion");

// On récupère le type depuis l'URL : admin, employe ou client
$type = $_GET['type'] ?? 'client';
?>

<?php include __DIR__ . "/../notification.php"; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <h1 class="h3 mb-3 text-center">
                <?php
                if ($type === 'admin') echo "Connexion Administrateur";
                elseif ($type === 'employe') echo "Connexion Employé";
                else echo "Connexion Client";
                ?>
            </h1>

            <form action="controller/auth/login_ctrl.php" method="POST">
                <div class="mb-3">
                    <label for="mail" class="form-label">Login (e-mail) :</label>
                    <input type="email" name="mail" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="psw" class="form-label">Mot de passe :</label>
                    <input type="password" name="psw" class="form-control" required>
                </div>

                <!-- Input caché pour envoyer le type au contrôleur -->
                <input type="hidden" name="type" value="<?= htmlspecialchars($type) ?>">

                <div class="text-center">
                    <input type="submit" class="btn btn-primary w-50" value="Connexion">
                </div>
            </form>
        </div>
    </div>
</div>