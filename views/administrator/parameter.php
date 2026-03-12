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
<section class="m-4">
<div class="mt-1 mb-4">
   <h1 class="title">Liste des paramétrages</h1>
</div>

<div class="row g-3">

<!-- Gestion des Devis & Factures -->
<div class="col-md-4 col-sm-6 col-12">
   <div class="card h-100 card-parametres shadow">
      <div class="card-header p-2 bg-gris-fonce text-white">
        <i class="bi bi-receipt-cutoff me-2"></i>Gestion des Devis & Factures
      </div>
      <div class="card-body p-0">
         <a class="nav-link w-100 p-2 menu-link" href="views/administrator/dashboard.php">- liste des moyens de paiements</a>
         <a class="nav-link w-100 p-2 menu-link" href="views/administrator/dashboard.php">- liste des TVA</a>
      </div>
      <div class="card-footer bg-gris-fonce"></div>
   </div>
</div>

<!-- Gestion des Clients -->
<div class="col-md-4 col-sm-6 col-12">
   <div class="card h-100 card-parametres">
      <div class="card-header p-2 bg-gris-fonce text-white">
        <i class="bi bi-people-fill me-2"></i>Gestion des Clients
      </div>
      <div class="card-body p-0">
         <a class="nav-link w-100 p-2 menu-link" href="views/administrator/settings/list_clients.php">- liste des clients</a>
         <a class="nav-link w-100 p-2 menu-link" href="views/administrator/dashboard.php">lien 2 crud</a>
         <a class="nav-link w-100 p-2 menu-link" href="views/administrator/dashboard.php">lien 3 crud</a>
      </div>
      <div class="card-footer bg-gris-fonce"></div>
   </div>
</div>

<!-- Gestion des Chantiers -->
<div class="col-md-4 col-sm-6 col-12">
   <div class="card h-100 card-parametres">
      <div class="card-header p-2 bg-gris-fonce text-white">
         <i class="bi bi-building me-2"></i>Gestion des Chantiers
      </div>
      <div class="card-body p-0">
         <a class="nav-link w-100 p-2 menu-link" href="views/administrator/dashboard.php">- liste des chantiers</a>
      </div>
      <div class="card-footer bg-gris-fonce"></div>
   </div>
</div>

<!-- Gestion des Communications -->
<div class="col-md-4 col-sm-6 col-12">
   <div class="card h-100 card-parametres">
      <div class="card-header p-2 bg-gris-fonce text-white">
         <i class="bi bi-chat-dots-fill me-2"></i>Gestion des Communications
      </div>
      <div class="card-body p-0">
         <a class="nav-link w-100 p-2 menu-link" href="views/administrator/settings/messages.php">- liste des messages</a>
         <a class="nav-link w-100 p-2 menu-link" href="views/administrator/dashboard.php">lien 2 crud</a>
         <a class="nav-link w-100 p-2 menu-link" href="views/administrator/dashboard.php">lien 3 crud</a>
      </div>
      <div class="card-footer bg-gris-fonce"></div>
   </div>
</div>

<!-- Gestion des Utilisateurs -->
<div class="col-md-4 col-sm-6 col-12">
   <div class="card h-100 card-parametres">
      <div class="card-header p-2 bg-gris-fonce text-white">
         <i class="bi bi-person-gear me-2"></i>Gestion des Utilisateurs
      </div>
      <div class="card-body p-0">
         <a class="nav-link w-100 p-2 menu-link" href="views/administrator/dashboard.php">- utilisateurs internes</a>
         <a class="nav-link w-100 p-2 menu-link" href="views/administrator/dashboard.php">- utilisateurs clients</a>
      </div>
      <div class="card-footer bg-gris-fonce"></div>
   </div>
</div>

<!-- Gestion des Paramètres -->
<div class="col-md-4 col-sm-6 col-12">
   <div class="card h-100 card-parametres">
      <div class="card-header p-2 bg-gris-fonce text-white">
         <i class="bi bi-gear-fill me-2"></i>Gestion des références
      </div>
      <div class="card-body p-0">
         <a class="nav-link w-100 p-2 menu-link" href="views/administrator/settings/add_reference.php">- Ajouter des références</a>
         <a class="nav-link w-100 p-2 menu-link" href="views/administrator/settings/list_reference.php">- Modifier une référence</a>
      </div>
      <div class="card-footer bg-gris-fonce"></div>
   </div>
</div>

</div>
</section>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
HTML;

// ----------------- INCLURE LAYOUT -----------------
require __DIR__ . '/../../layout.php';
