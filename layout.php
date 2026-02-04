<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/head.php'; // contient head_with_title()

// On peut mettre un titre dynamique ou par défaut
$title = $title ?? "Bienvenue";

// Appel de la fonction pour afficher le <head> et ouvrir <body>
head_with_title($title);
?>

    <!-- ================= NAVBAR HORIZONTALE (TOP) ================= -->
    <nav class="navbar navbar-light bg-body-tertiary px-4">
        <!-- Logo -->
        <a class="navbar-brand" href="./">
            <img src="assets/statics/images/logo.png" alt="Logo de l'entreprise" style="height:80px; margin-right:10px;">
        </a>

        <!-- Connexion / User -->
       <div class="ms-auto dropdown">
    <?php if (!isUserLoggedIn()): ?>
<a href="views/login.php"
   class="btn"
   style="background:#e38f3c; color:#fff;"
   onmouseover="this.style.background='#41403b'"
   onmouseout="this.style.background='#e38f3c'">
    Connexion
</a>
    <?php else: ?>
        <?php
            $role = getUserRole(); // "admin" ou "client"
            $roleLabel = ($role === 'administrateur') ? 'Administrateur' : 'Client';
        ?>

        <!-- Bouton dropdown -->
<a class="d-flex align-items-center text-decoration-none"
   href="#"
   role="button"
   data-bs-toggle="dropdown"
   aria-expanded="false"
   style="color:#41403b;">

    <div class="text-end">
        <div class="d-flex align-items-center justify-content-end text-uppercase">
            <span><?= htmlentities($_SESSION["name"]) ?></span>

        </div>
        <small class="text-muted text-uppercase"><?= $roleLabel ?></small>            <span class="ms-1 dropdown-toggle"></span>
    </div>
</a>

        <!-- Menu dropdown -->
        <ul class="dropdown-menu dropdown-menu-end">
            <?php if ($role === 'admin'): ?>
                <li>
                    <a class="dropdown-item" href="admin/dashboard.php">
                        <i class="bi bi-speedometer2 me-2"></i>Dashboard admin
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
            <?php endif; ?>

            <li>
                <a class="dropdown-item text-end" href="controller/auth/logout_ctrl.php" style="color:#41403b;">
                    Déconnexion
                </a>
            </li>
        </ul>
    <?php endif; ?>
</div>
    </nav>

    <!-- ================= LAYOUT PRINCIPAL ================= -->
    <div class="d-flex">

        <!-- ================= SIDEBAR VERTICALE ================= -->
        <nav class="vh-100 p-3" style="width: 280px; background:#e38f3c;">
            <ul class="nav flex-column gap-2">
                <div class="mt-3 text-decoration-underline">
                    <h6>NOTRE ENTREPRISE</h6>
                </div>
                <!-- Navigation générale -->
                <li class="nav-item"> <a class="nav-link rounded text-white" href="views/homepage.php"
                        onmouseover="this.style.background='#41403b';"
                        onmouseout="this.style.background='';">
                        Accueil
                    </a></li>
                <li class="nav-item"> <a class="nav-link rounded text-white" href="#"
                        onmouseover="this.style.background='#41403b';"
                        onmouseout="this.style.background='';">Nos réalisations</a></li>
                <li class="nav-item"> <a class="nav-link rounded text-white" href="#"
                        onmouseover="this.style.background='#41403b';"
                        onmouseout="this.style.background='';">Contact</a></li>

                <?php if (isUserLoggedIn()): ?>
                    <?php $role = getUserRole(); ?>

                    <!-- CLIENT -->
                    <?php if ($role === "client"): ?>
                        <div class="mt-3 text-decoration-underline">
                            <h6>ESPACE CLIENT</h6>
                        </div>
                        <li class="nav-item">
                            <a class="nav-link rounded text-white" href="#"
                                onmouseover="this.style.background='#41403b';"
                                onmouseout="this.style.background='';">
                                <i class="bi bi-speedometer2 me-2"></i>Tableau de bord
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link rounded text-white" href="#"
                                onmouseover="this.style.background='#41403b';"
                                onmouseout="this.style.background='';">
                                <i class="bi bi-gear me-2"></i>Paramétrages
                            </a>
                        </li>

                        <!-- ADMIN -->
                    <?php elseif ($role === "administrateur"): ?>
                        <div class="mt-3 text-decoration-underline">
                            <h6>ESPACE ADMINISTRATEUR</h6>
                        </div>
                        <li class="nav-item">
                            <a class="nav-link rounded text-white" href="views/administrateur/dashboard.php"
                                onmouseover="this.style.background='#41403b';"
                                onmouseout="this.style.background='';">
                                <i class="bi bi-speedometer2 me-2"></i>Tableau de bord
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link rounded text-white" href="views/administrateur/parameter.php"
                                onmouseover="this.style.background='#41403b';"
                                onmouseout="this.style.background='';">
                                <i class="bi bi-gear me-2"></i>Paramétrages
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
        </nav>

        <!-- ================= CONTENU PRINCIPAL ================= -->
        <main class="flex-fill p-4">
            <?php
            // Ici on affiche le contenu spécifique injecté depuis la page
            if (isset($content)) {
                echo $content;
            }
            ?>
        </main>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>