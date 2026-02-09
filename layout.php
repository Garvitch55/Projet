<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/head.php'; // contient head_with_title()

// On peut mettre un titre dynamique ou par défaut
$title = $title ?? "Bienvenue";

// Appel de la fonction pour afficher le <head> et ouvrir <body>
head_with_title($title);
?>

<!-- ================= NAVBAR HORIZONTALE (TOP) ================= -->
<nav class="navbar navbar-light bg-body-tertiary px-4 shadow">
    <!-- Logo -->
    <a class="navbar-brand" href="./">
        <img src="assets/statics/images/logo.png" alt="Logo de l'entreprise" style="height:80px; margin-right:10px;">
    </a>

    <!-- Connexion / User -->
    <div class="ms-auto">
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
            $role = getUserRole(); // "administrateur" ou "client"
            $roleLabel = ($role === 'administrateur') ? 'Administrateur' : 'Client';
            ?>
            <!-- Dropdown correctement structuré -->
            <div class="dropdown">
                <a class="btn btn-light dropdown-toggle d-flex align-items-center" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="me-2 text-start text-uppercase">
                        <div><?= htmlentities($_SESSION["name"]) ?></div>
                        <div><?= $roleLabel ?></div>
                    </div>
                </a>
<ul class="dropdown-menu dropdown-menu-end dropdown-menu-border">
  <li class="p-2 me-2 text-end">
    <a href="controller/auth/logout_ctrl.php" 
       class="text-white text-decoration-none">
      <i class="bi bi-box-arrow-right me-2"></i>
      Déconnexion
    </a>
  </li>
</ul>
            </div>
        <?php endif; ?>
    </div>
</nav>

<!-- ================= LAYOUT PRINCIPAL ================= -->
<div class="d-flex">

    <!-- ================= SIDEBAR VERTICALE ================= -->
    <nav class="vh-100 p-3 bg-orange-fonce" style="width: 280px;">
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
            <li class="nav-item"> <a class="nav-link rounded text-white" href="views/reference.php"
                    onmouseover="this.style.background='#41403b';"
                    onmouseout="this.style.background='';">Nos réalisations</a></li>
            <li class="nav-item"> <a class="nav-link rounded text-white" href="views/contact.php"
                    onmouseover="this.style.background='#41403b';"
                    onmouseout="this.style.background='';">Nous contacter</a></li>

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
        <i class="bi bi-speedometer2 me-2"></i> Tableau de bord
    </a>
</li>

<li class="nav-item">
    <a class="nav-link rounded text-white" href="views/administrateur/customer.php"
        onmouseover="this.style.background='#41403b';"
        onmouseout="this.style.background='';">
        <i class="bi bi-people-fill me-2"></i> Clients
    </a>
</li>

<li class="nav-item">
    <a class="nav-link rounded text-white" href="views/administrateur/project.php"
        onmouseover="this.style.background='#41403b';"
        onmouseout="this.style.background='';">
        <i class="bi bi-building me-2"></i> Chantiers
    </a>
</li>

<li class="nav-item">
    <a class="nav-link rounded text-white" href="views/administrateur/quotation.php"
        onmouseover="this.style.background='#41403b';"
        onmouseout="this.style.background='';">
        <i class="bi bi-receipt me-2"></i> Devis
    </a>
</li>

<li class="nav-item">
    <a class="nav-link rounded text-white" href="views/administrateur/feature.php"
        onmouseover="this.style.background='#41403b';"
        onmouseout="this.style.background='';">
        <i class="bi bi-file-earmark-text me-2"></i> Factures
    </a>
</li>
<a class="nav-link rounded text-white" href="views/administrateur/parameter.php" onmouseover="this.style.background='#41403b';" onmouseout="this.style.background='';"> <i class="bi bi-gear me-2"></i>Paramétrages </a>
                <?php endif; ?>
            <?php endif; ?>
        </ul>
    </nav>

    <!-- ================= CONTENU PRINCIPAL ================= -->
    <main class="flex-fill">
       
       <?php
        // Ici on affiche le contenu spécifique injecté depuis la page
                if (isset($notification)) {
            echo $notification;
        }
       ?>
       <div class="p-3">
       <?php 
        if (isset($content)) {
            echo $content;
        }
        ?>
        </div>
    </main>

</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>