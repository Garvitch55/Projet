<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/head.php'; // contient head_with_title()

// Titre dynamique par défaut
$title = $title ?? 'Bienvenue';

// Appel de la fonction pour afficher le <head> et ouvrir <body>
head_with_title($title);

// -------------------------
// Détection de la page courante pour le menu actif
$current_page = basename($_SERVER['PHP_SELF']);
$current_path = $_SERVER['REQUEST_URI'];

// Définir les pages et dossiers qui font partie du menu Paramétrages
$is_param_active = false;
if (
    strpos($current_path, '/administrator/parameter.php') !== false ||
    strpos($current_path, '/administrator/settings/') !== false
) {
    $is_param_active = true;
}

// -------------------------
// Récupérer le nombre de messages non lus si admin
$unread_count = 0;
$latest_messages = [];
if (isUserLoggedIn() && getUserRole() === 'administrateur') {
    $pdo = getPDO();
    $stmt = $pdo->query("SELECT COUNT(*) as unread FROM contact WHERE is_read = 0");
    $unread_count = (int)$stmt->fetch()['unread'];

    // Récupérer les derniers messages avec id_contact
    $stmt2 = $pdo->query("SELECT id_contact, first_name, last_name, subject, created_at, is_read FROM contact ORDER BY created_at DESC LIMIT 3");
    $latest_messages = $stmt2->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!-- ================= NAVBAR HORIZONTALE (TOP) ================= -->
<nav class="navbar navbar-light bg-body-tertiary px-4 shadow">
    <!-- Logo -->
    <a class="navbar-brand logo" href="./">
        <img src="assets/statics/images/logo.png" alt="Logo de l'entreprise" style="height:80px; margin-right:10px;">
    </a>

    <!-- Connexion / User -->
    <div class="ms-auto d-flex align-items-center">

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

            <?php if ($role === 'administrateur'): ?>
            <!-- Cloche notifications -->
            <div class="dropdown me-3">
                <a class="btn-cloche position-relative text-white" href="#" role="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa-solid fa-bell fa-shake"></i>
                    <?php if ($unread_count > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            <?= $unread_count ?>
                            <span class="visually-hidden">messages non lus</span>
                        </span>
                    <?php endif; ?>
                </a>
                <ul class="mt-3 dropdown-menu dropdown-menu-end dropdown-menu-bell" aria-labelledby="notificationDropdown">
                    <?php if (!empty($latest_messages)): ?>
                        <div class="d-flex flex-column px-3">
                            <?php foreach ($latest_messages as $msg): ?>
                                <?php $msg_id = $msg['id_contact']; ?>
                                <a href="views/administrator/settings/view_messenger_contact.php?id=<?= $msg['id_contact'] ?>&action=read" class="text-decoration-none">
                                    <li class="dropdown-item text-white d-flex align-items-start gap-2 bell-message rounded-1">

                                        <!-- Contenu du message -->
                                        <div class="flex-grow-1">
                                            <p class="m-0 fw-bold"><?= htmlentities($msg['first_name'].' '.$msg['last_name']) ?></p>
                                            <p class="m-0"><?= htmlentities($msg['subject']) ?></p>
                                            <small class="text-white"><?= htmlentities($msg['created_at']) ?></small>
                                        </div>

                                        <!-- Badge à droite -->
                                        <div class="ms-auto">
                                            <?php if(isset($msg['is_read']) && $msg['is_read'] == 1): ?>
                                                <span class="badge bg-success">Lu</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Non lu</span>
                                            <?php endif; ?>
                                        </div>

                                    </li>
                                </a>
                                <li><hr class="dropdown-divider text-white"></li>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <li class="dropdown-item text-center text-white">Aucun message</li>
                    <?php endif; ?>

                    <!-- Bouton centré -->
                    <div class="text-center pb-2">
                        <li>
                            <a class="btn5" href="views/administrator/settings/messenger_contact.php">
                                Voir tous les messages
                            </a>
                        </li>
                    </div>
                </ul>
            </div>
            <?php endif; ?>

            <!-- Menu utilisateur -->
            <div class="dropdown">
                <a class="btn-user dropdown-toggle d-flex align-items-center" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="me-2 text-start">
                        <div class="text-uppercase"><?= htmlentities($_SESSION['name']) ?></div>
                        <div><?= $roleLabel ?></div>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-user">
                    <li class="p-2 me-2 text-end">
                        <a href="controller/auth/logout_ctrl.php" class="text-white text-decoration-none shadow">
                            <i class="bi bi-box-arrow-right me-2"></i>Déconnexion
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
    <nav class="p-3 bg-orange-fonce w-20 sidebar-vertical">
        <ul class="nav flex-column">
            <div class="mt-3 text-decoration-underline">
                <h6>NOTRE ENTREPRISE</h6>
            </div>

            <!-- Navigation générale -->
            <li class="nav-item">
                <a class="nav-link rounded text-white <?= ($current_page=='homepage.php') ? 'active' : '' ?>" href="views/homepage.php">Accueil</a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded text-white <?= ($current_page=='service.php') ? 'active' : '' ?>" href="views/service.php">Nos services</a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded text-white <?= ($current_page=='reference.php') ? 'active' : '' ?>" href="views/reference.php">Nos réalisations</a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded text-white <?= ($current_page=='contact.php') ? 'active' : '' ?>" href="views/contact.php">Nous contacter</a>
            </li>

            <?php if (isUserLoggedIn()): ?>
                <?php $role = getUserRole(); ?>

                <!-- CLIENT -->
                <?php if ($role === 'client'): ?>
                    <div class="mt-3 text-decoration-underline">
                        <h6>ESPACE CLIENT</h6>
                    </div>
                    <li class="nav-item">
                        <a class="nav-link rounded text-white <?= ($current_page=='dashboard.php') ? 'active' : '' ?>" href="views/customer/dashboard.php"><i class="fa-solid fa-chart-line fa-beat me-2"></i>Tableau de bord</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link rounded text-white <?= ($current_page=='quotation.php') ? 'active' : '' ?>" href="views/customer/quotation.php"><i class="fa-solid fa-file-signature fa-beat me-2"></i> Devis</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link rounded text-white <?= ($current_page=='invoice.php') ? 'active' : '' ?>" href="views/customer/invoice.php"><i class="fa-solid fa-file-invoice fa-bounce me-3"></i> Factures</a>
                    </li>

                <!-- ADMIN -->
                <?php elseif ($role === 'administrateur'): ?>
                    <div class="mt-3 text-decoration-underline">
                        <h6>ESPACE ADMINISTRATEUR</h6>
                    </div>

                    <li class="nav-item">
                        <a class="nav-link rounded text-white <?= ($current_page=='dashboard.php') ? 'active' : '' ?>" href="views/administrator/dashboard.php"><i class="fa-solid fa-chart-line fa-beat me-2"></i> Tableau de bord</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link rounded text-white <?= ($current_page=='customer.php') ? 'active' : '' ?>" href="views/administrator/customer.php"><i class="fa-solid fa-users fa-beat  me-2"></i> Clients</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link rounded text-white <?= ($current_page=='project.php') ? 'active' : '' ?>" href="views/administrator/project.php"><i class="fa-solid fa-helmet-safety fa-beat  me-2"></i> Chantiers</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link rounded text-white <?= ($current_page=='quotation.php') ? 'active' : '' ?>" href="views/administrator/quotation.php"><i class="fa-solid fa-file-signature fa-beat me-2"></i> Devis</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link rounded text-white <?= ($current_page=='invoice.php') ? 'active' : '' ?>" href="views/administrator/invoice.php"><i class="fa-solid fa-file-invoice fa-bounce me-3"></i> Factures</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link rounded text-white <?= $is_param_active ? 'active' : '' ?>" href="views/administrator/parameter.php"><i class="fa-solid fa-gear fa-spin-pulse me-2"></i> Paramétrages</a>
                    </li>
                <?php endif; ?>
            <?php endif; ?>
        </ul>
    </nav>

    <!-- ================= CONTENU PRINCIPAL ================= -->
    <main class="flex-fill w-80">
        <?php if (isset($notification)) echo $notification; ?>
        <div class="p-3">
            <?php if (isset($content)) echo $content; ?>
        </div>
    </main>

</div>

<footer class="text-white mt-auto py-3">
  <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center">

    <div>
      &copy; <?= date('Y') ?> A.GARNIER CONSTRUCTION. Tous droits réservés.
    </div>

    <div class="mt-2 mt-md-0">
      <a href="#" class="text-white me-3">Contact</a>
      <a href="#" class="text-white me-3">Mentions légales</a>
      <a href="#" class="text-white">Politique de confidentialité</a>
    </div>
  </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>