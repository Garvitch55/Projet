<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../head.php';    // head_with_title

// On récupère le type d'utilisateur depuis l'URL
$type = $_GET['type'] ?? 'client';

// On capture le contenu spécifique pour le <main>
ob_start();
?>
<?php
echo password_hash("Garvitch_55100", PASSWORD_DEFAULT);
?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <h1 class="h3 mb-3 text-center text-gris-fonce">
Se connecter
            </h1>

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
                    <button type="submit"
    class="btn w-50 text-white"
    style="background:#e38f3c;"
    onmouseover="this.style.background='#41403b';"
    onmouseout="this.style.background='#e38f3c';">
    Connexion
</button>

<a class="nav-link rounded text-gris-fonce pt-2" href="views/auth/add_client_form.php"
>
                    Inscription
                </a>






                </div>
            </form>
        </div>
    </div>
</div>
<?php
// On stocke le contenu dans $content pour le layout
$content = ob_get_clean();

// On inclut le layout qui affichera navbar/sidebar et injectera $content dans <main>
require __DIR__ . '/../layout.php';