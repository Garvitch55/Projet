<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../head.php';    // head_with_title

// On récupère le type d'utilisateur depuis l'URL
$type = $_GET['type'] ?? 'administrateur';

// On capture le contenu spécifique pour le <main>
ob_start();
?>
<div class="container mt-1 mb-4 border-bottom border-dark">
   <h6>Paramétrages</h6>
</div>




<div class="row g-3">
<div class="col-md-4 col-sm-6 col-12">
   <div class="card h-100" style="border:1px solid #e38f3c; background: radial-gradient(
    circle at top left,
    rgba(227, 143, 60, 0.6) 2%,
    #ffffff 100%
);">
      <div class="card-header p-2" style="background:#41403b;color:#e38f3c;">
        <i class="bi bi-receipt-cutoff me-2"></i>Gestion des Devis & Factures
      </div>
      <div class="card-body p-0">
         <a class="nav-link w-100 p-2" href="views/administrateur/dashboard.php"
            onmouseover="this.style.background='#e38f3c'; this.style.color='white';"
            onmouseout="this.style.background=''; this.style.color='';">
            lien 1 crud
         </a>
         <a class="nav-link w-100 p-2" href="views/administrateur/dashboard.php"
            onmouseover="this.style.background='#e38f3c'; this.style.color='white';"
            onmouseout="this.style.background=''; this.style.color='';">
            lien 2 crud
         </a>
         <a class="nav-link w-100 p-2" href="views/administrateur/dashboard.php"
            onmouseover="this.style.background='#e38f3c'; this.style.color='white';"
            onmouseout="this.style.background=''; this.style.color='';">
            lien 3 crud
         </a>
      </div>
      <div class="card-footer" style="background:#41403b;"></div>
   </div>
</div>

<div class="col-md-4 col-sm-6 col-12">
      <div class="card h-100" style="border:1px solid #e38f3c; background: radial-gradient(
    circle at top left,
    rgba(227, 143, 60, 0.6) 2%,
    #ffffff 100%
);">
      <div class="card-header p-2" style="background:#41403b;color:#e38f3c;">
        <i class="bi bi-people-fill me-2"></i>Gestion des Clients
      </div>
      <div class="card-body p-0">
         <a class="nav-link w-100 p-2" href="views/administrateur/dashboard.php"
            onmouseover="this.style.background='#e38f3c'; this.style.color='white';"
            onmouseout="this.style.background=''; this.style.color='';">
            lien 1 crud
         </a>
         <a class="nav-link w-100 p-2" href="views/administrateur/dashboard.php"
            onmouseover="this.style.background='#e38f3c'; this.style.color='white';"
            onmouseout="this.style.background=''; this.style.color='';">
            lien 2 crud
         </a>
         <a class="nav-link w-100 p-2" href="views/administrateur/dashboard.php"
            onmouseover="this.style.background='#e38f3c'; this.style.color='white';"
            onmouseout="this.style.background=''; this.style.color='';">
            lien 3 crud
         </a>
      </div>
      <div class="card-footer" style="background:#41403b;"></div>
   </div>
</div>

<div class="col-md-4 col-sm-6 col-12">
      <div class="card h-100" style="border:1px solid #e38f3c; background: radial-gradient(
    circle at top left,
    rgba(227, 143, 60, 0.6) 2%,
    #ffffff 100%
);">
      <div class="card-header p-2" style="background:#41403b;color:#e38f3c;">
         <i class="bi bi-building me-2"></i>Gestion des Chantiers
      </div>
      <div class="card-body p-0">
         <a class="nav-link w-100 p-2" href="views/administrateur/dashboard.php"
            onmouseover="this.style.background='#e38f3c'; this.style.color='white';"
            onmouseout="this.style.background=''; this.style.color='';">
            lien 1 crud
         </a>
         <a class="nav-link w-100 p-2" href="views/administrateur/dashboard.php"
            onmouseover="this.style.background='#e38f3c'; this.style.color='white';"
            onmouseout="this.style.background=''; this.style.color='';">
            lien 2 crud
         </a>
         <a class="nav-link w-100 p-2" href="views/administrateur/dashboard.php"
            onmouseover="this.style.background='#e38f3c'; this.style.color='white';"
            onmouseout="this.style.background=''; this.style.color='';">
            lien 3 crud
         </a>
      </div>
      <div class="card-footer" style="background:#41403b;"></div>
   </div>
</div>

<div class="col-md-4 col-sm-6 col-12">
      <div class="card h-100" style="border:1px solid #e38f3c; background: radial-gradient(
    circle at top left,
    rgba(227, 143, 60, 0.6) 2%,
    #ffffff 100%
);">
      <div class="card-header p-2" style="background:#41403b;color:#e38f3c;">
         <i class="bi bi-chat-dots-fill me-2"></i>Gestion des Communications
      </div>
      <div class="card-body p-0">
         <a class="nav-link w-100 p-2" href="views/administrateur/dashboard.php"
            onmouseover="this.style.background='#e38f3c'; this.style.color='white';"
            onmouseout="this.style.background=''; this.style.color='';">
            lien 1 crud
         </a>
         <a class="nav-link w-100 p-2" href="views/administrateur/dashboard.php"
            onmouseover="this.style.background='#e38f3c'; this.style.color='white';"
            onmouseout="this.style.background=''; this.style.color='';">
            lien 2 crud
         </a>
         <a class="nav-link w-100 p-2" href="views/administrateur/dashboard.php"
            onmouseover="this.style.background='#e38f3c'; this.style.color='white';"
            onmouseout="this.style.background=''; this.style.color='';">
            lien 3 crud
         </a>
      </div>
      <div class="card-footer" style="background:#41403b;"></div>
   </div>
</div>
<div class="col-md-4 col-sm-6 col-12">
      <div class="card h-100" style="border:1px solid #e38f3c; background: radial-gradient(
    circle at top left,
    rgba(227, 143, 60, 0.6) 2%,
    #ffffff 100%
);">
      <div class="card-header p-2" style="background:#41403b;color:#e38f3c;">
         <i class="bi bi-person-gear me-2"></i>Gestion des Utilisateurs
      </div>
      <div class="card-body p-0">
         <a class="nav-link w-100 p-2" href="views/administrateur/dashboard.php"
            onmouseover="this.style.background='#e38f3c'; this.style.color='white';"
            onmouseout="this.style.background=''; this.style.color='';">
            lien 1 crud
         </a>
         <a class="nav-link w-100 p-2" href="views/administrateur/dashboard.php"
            onmouseover="this.style.background='#e38f3c'; this.style.color='white';"
            onmouseout="this.style.background=''; this.style.color='';">
            lien 2 crud
         </a>
         <a class="nav-link w-100 p-2" href="views/administrateur/dashboard.php"
            onmouseover="this.style.background='#e38f3c'; this.style.color='white';"
            onmouseout="this.style.background=''; this.style.color='';">
            lien 3 crud
         </a>
      </div>
      <div class="card-footer" style="background:#41403b;"></div>
   </div>
</div>
<div class="col-md-4 col-sm-6 col-12">
      <div class="card h-100" style="border:1px solid #e38f3c; background: radial-gradient(
    circle at top left,
    rgba(227, 143, 60, 0.6) 2%,
    #ffffff 100%
);">
      <div class="card-header p-2" style="background:#41403b;color:#e38f3c;">
         <i class="bi bi-gear-fill me-2"></i>Gestion des Paramètres
      </div>
      <div class="card-body p-0">
          <a class="nav-link w-100 p-2 menu-link" style="border-bottom:1px solid #e38f3c;" href="views/administrateur/dashboard.php"
            onmouseover="this.style.background='#e38f3c'; this.style.color='white';"
            onmouseout="this.style.background=''; this.style.color='';">
            - gestion des références
         </a>
         <a class="nav-link w-100 p-2 menu-link" style="border-bottom:1px solid #e38f3c;" href="views/administrateur/dashboard.php"
            onmouseover="this.style.background='#e38f3c'; this.style.color='white';"
            onmouseout="this.style.background=''; this.style.color='';">
            - gestion des utiltisateurs
         </a>
         <a class="nav-link w-100 p-2 menu-link" href="views/administrateur/dashboard.php"
            onmouseover="this.style.background='#e38f3c'; this.style.color='white';"
            onmouseout="this.style.background=''; this.style.color='';">
            lien 3 crud
         </a>




      </div>
      <div class="card-footer" style="background:#41403b;"></div>
   </div>
</div>
</div>



<?php
// On stocke le contenu dans $content pour le layout
$content = ob_get_clean();

// On inclut le layout qui affichera navbar/sidebar et injectera $content dans <main>
require __DIR__ . '/../../layout.php';
