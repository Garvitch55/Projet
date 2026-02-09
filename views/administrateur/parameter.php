<?php
require_once __DIR__ . '/../../config.php';

// On récupère le type d'utilisateur depuis l'URL
$type = $_GET['type'] ?? 'administrateur';

// Pas connecté → dehors
if (!isset($_SESSION['id'])) {
    header("Location: login.php?status=danger&message=Veuillez vous connecter.");
    exit;
}

// Pas admin → dehors
if ($_SESSION['role'] !== 'administrateur') {
    header("Location: ../../index.php?status=danger&message=Accès refusé.");
    exit;
}

require_once __DIR__ . '/../../head.php';    // head_with_title
$title = "Paramétrages";

// ----------------- CONTENT -----------------
$content = <<<HTML
<div class="container mt-1 mb-4 border-bottom border-dark">
   <h6>Liste des paramétrages</h6>
</div>

<div class="row g-3">

<!-- Gestion des Devis & Factures -->
<div class="col-md-4 col-sm-6 col-12">
   <div class="card h-100 card-parametres" style="border:1px solid #e38f3c; background: rgba(227, 143, 60, 0.6);">
      <div class="card-header p-2 bg-gris-fonce text-white">
        <i class="bi bi-receipt-cutoff me-2"></i>Gestion des Devis & Factures
      </div>
      <div class="card-body p-0">
         <a class="nav-link w-100 p-2" style="border-bottom:1px solid #e38f3c;" href="views/administrateur/dashboard.php"
            onmouseover="this.style.background='#e38f3c'; this.style.color='white';"
            onmouseout="this.style.background=''; this.style.color='';">- liste des moyens de paiements</a>
         <a class="nav-link w-100 p-2" style="border-bottom:1px solid #e38f3c;" href="views/administrateur/dashboard.php"
            onmouseover="this.style.background='#e38f3c'; this.style.color='white';"
            onmouseout="this.style.background=''; this.style.color='';">- liste des TVA</a>
      </div>
      <div class="card-footer bg-gris-fonce"></div>
   </div>
</div>

<!-- Gestion des Clients -->
<div class="col-md-4 col-sm-6 col-12">
   <div class="card h-100 card-parametres" style="border:1px solid #e38f3c; background: rgba(227, 143, 60, 0.6);">
      <div class="card-header p-2 bg-gris-fonce text-white">
        <i class="bi bi-people-fill me-2"></i>Gestion des Clients
      </div>
      <div class="card-body p-0">
         <a class="nav-link w-100 p-2" style="border-bottom:1px solid #e38f3c;" href="views/administrateur/dashboard.php"
            onmouseover="this.style.background='#e38f3c'; this.style.color='white';"
            onmouseout="this.style.background=''; this.style.color='';">lien 1 crud</a>
         <a class="nav-link w-100 p-2" style="border-bottom:1px solid #e38f3c;" href="views/administrateur/dashboard.php"
            onmouseover="this.style.background='#e38f3c'; this.style.color='white';"
            onmouseout="this.style.background=''; this.style.color='';">lien 2 crud</a>
         <a class="nav-link w-100 p-2" style="border-bottom:1px solid #e38f3c;" href="views/administrateur/dashboard.php"
            onmouseover="this.style.background='#e38f3c'; this.style.color='white';"
            onmouseout="this.style.background=''; this.style.color='';">lien 3 crud</a>
      </div>
      <div class="card-footer bg-gris-fonce"></div>
   </div>
</div>

<!-- Gestion des Chantiers -->
<div class="col-md-4 col-sm-6 col-12">
   <div class="card h-100 card-parametres" style="border:1px solid #e38f3c; background: rgba(227, 143, 60, 0.6);">
      <div class="card-header p-2 bg-gris-fonce text-white">
         <i class="bi bi-building me-2"></i>Gestion des Chantiers
      </div>
      <div class="card-body p-0">
         <a class="nav-link w-100 p-2" style="border-bottom:1px solid #e38f3c;" href="views/administrateur/dashboard.php"
            onmouseover="this.style.background='#e38f3c'; this.style.color='white';"
            onmouseout="this.style.background=''; this.style.color='';">- liste des chantiers</a>
      </div>
      <div class="card-footer bg-gris-fonce"></div>
   </div>
</div>

<!-- Gestion des Communications -->
<div class="col-md-4 col-sm-6 col-12">
   <div class="card h-100 card-parametres" style="border:1px solid #e38f3c; background: rgba(227, 143, 60, 0.6);">
      <div class="card-header p-2 bg-gris-fonce text-white">
         <i class="bi bi-chat-dots-fill me-2"></i>Gestion des Communications
      </div>
      <div class="card-body p-0">
         <a class="nav-link w-100 p-2" style="border-bottom:1px solid #e38f3c;" href="views/administrateur/dashboard.php"
            onmouseover="this.style.background='#e38f3c'; this.style.color='white';"
            onmouseout="this.style.background=''; this.style.color='';">lien 1 crud</a>
         <a class="nav-link w-100 p-2" style="border-bottom:1px solid #e38f3c;" href="views/administrateur/dashboard.php"
            onmouseover="this.style.background='#e38f3c'; this.style.color='white';"
            onmouseout="this.style.background=''; this.style.color='';">lien 2 crud</a>
         <a class="nav-link w-100 p-2" style="border-bottom:1px solid #e38f3c;" href="views/administrateur/dashboard.php"
            onmouseover="this.style.background='#e38f3c'; this.style.color='white';"
            onmouseout="this.style.background=''; this.style.color='';">lien 3 crud</a>
      </div>
      <div class="card-footer bg-gris-fonce card-parametres"></div>
   </div>
</div>

<!-- Gestion des Utilisateurs -->
<div class="col-md-4 col-sm-6 col-12">
   <div class="card h-100 card-parametres" style="border:1px solid #e38f3c; background: rgba(227, 143, 60, 0.6);">
      <div class="card-header p-2 bg-gris-fonce text-white">
         <i class="bi bi-person-gear me-2"></i>Gestion des Utilisateurs
      </div>
      <div class="card-body p-0">
         <a class="nav-link w-100 p-2" style="border-bottom:1px solid #e38f3c;" href="views/administrateur/dashboard.php"
            onmouseover="this.style.background='#e38f3c'; this.style.color='white';"
            onmouseout="this.style.background=''; this.style.color='';">- utilisateurs internes</a>
         <a class="nav-link w-100 p-2" style="border-bottom:1px solid #e38f3c;" href="views/administrateur/dashboard.php"
            onmouseover="this.style.background='#e38f3c'; this.style.color='white';"
            onmouseout="this.style.background=''; this.style.color='';">- utilisateurs clients</a>
      </div>
      <div class="card-footer bg-gris-fonce"></div>
   </div>
</div>

<!-- Gestion des Paramètres -->
<div class="col-md-4 col-sm-6 col-12">
   <div class="card h-100 card-parametres" style="border:1px solid #e38f3c; background: rgba(227, 143, 60, 0.6);">
      <div class="card-header p-2 bg-gris-fonce text-white">
         <i class="bi bi-gear-fill me-2"></i>Gestion des Paramètres
      </div>
      <div class="card-body p-0">
         <a class="nav-link w-100 p-2 menu-link" style="border-bottom:1px solid #e38f3c;" href="views/administrateur/dashboard.php"
            onmouseover="this.style.background='#e38f3c'; this.style.color='white';"
            onmouseout="this.style.background=''; this.style.color='';">- gestion des références</a>
         <a class="nav-link w-100 p-2 menu-link" style="border-bottom:1px solid #e38f3c;" href="views/administrateur/dashboard.php"
            onmouseover="this.style.background='#e38f3c'; this.style.color='white';"
            onmouseout="this.style.background=''; this.style.color='';">- gestion des utiltisateurs</a>
      </div>
      <div class="card-footer bg-gris-fonce"></div>
   </div>
</div>

</div><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
HTML;

// ----------------- INCLURE LAYOUT -----------------
require __DIR__ . '/../../layout.php';
