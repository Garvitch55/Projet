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

    <!-- Gestion des Communications -->
    <div class="col-12 col-sm-6 col-md-6 col-lg-4">
       <div class="card h-100 card-parametres">
          <div class="card-header p-2 bg-gris-fonce text-white">
             <i class="bi bi-chat-dots-fill me-2"></i>Gestion des Communications
          </div>
          <div class="card-body p-0">
             <a class="nav-link w-100 p-2 menu-link" href="views/administrator/settings/messenger_contact.php">- liste des messages des contacts</a>
             <a class="nav-link w-100 p-2 menu-link mb-4" href="views/administrator/settings/messenger_customer.php">- liste des messages clients</a>
             
          </div>
          <div class="card-footer bg-gris-fonce"></div>
       </div>
    </div>

    <!-- Gestion des Utilisateurs -->
    <div class="col-12 col-sm-6 col-md-6 col-lg-4">
       <div class="card h-100 card-parametres">
          <div class="card-header p-2 bg-gris-fonce text-white">
             <i class="bi bi-person-gear me-2"></i>Gestion des Utilisateurs
          </div>
          <div class="card-body p-0">
             <a class="nav-link w-100 p-2 menu-link" href="views/administrator/settings/list_staff.php">- liste du personnel</a>

          </div>
          <div class="card-footer bg-gris-fonce"></div>
       </div>
    </div>

    <!-- Gestion des Références -->
    <div class="col-12 col-sm-6 col-md-6 col-lg-4">
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

    <!-- Gestion des Devis & Factures -->
    <div class="col-12 col-sm-6 col-md-6 col-lg-4">
       <div class="card h-100 card-parametres shadow">
          <div class="card-header p-2 bg-gris-fonce text-white">
            <i class="bi bi-receipt-cutoff me-2"></i>Gestion des Devis & Factures
          </div>
          <div class="card-body p-0">
             <a class="nav-link w-100 p-2 menu-link" href="views/administrator/settings/payement.php">- liste des moyens de paiements</a>
             <a class="nav-link w-100 p-2 menu-link" href="views/administrator/settings/list_tva.php">- liste des TVA</a>
             <a class="nav-link w-100 p-2 menu-link" href="views/administrator/settings/term.php">- liste des terms</a>
          </div>
          <div class="card-footer bg-gris-fonce"></div>
       </div>
    </div>

    <!-- Gestion des bibliothèques des ouvrages -->
    <div class="col-12 col-sm-6 col-md-6 col-lg-4">
       <div class="card h-100 card-parametres">
          <div class="card-header p-2 bg-gris-fonce text-white">
             <i class="bi bi-building me-2"></i>Gestion des bibliothèques des ouvrages
          </div>
          <div class="card-body p-0">
             <a class="nav-link w-100 p-2 menu-link" href="views/administrator/settings/work.php">- liste des ouvrages</a>
          </div>
          <div class="card-footer bg-gris-fonce"></div>
       </div>
    </div>

    <!-- Gestion pour chantier -->
    <div class="col-12 col-sm-6 col-md-6 col-lg-4">
       <div class="card h-100 card-parametres">
          <div class="card-header p-2 bg-gris-fonce text-white">
             <i class="bi bi-building me-2"></i>Gestion pour chantier
          </div>
          <div class="card-body p-0">
             <a class="nav-link w-100 p-2 menu-link" href="views/administrator/settings/material.php">- liste du matériel</a>
             <a class="nav-link w-100 p-2 menu-link" href="views/administrator/settings/workforce.php">- liste de la main d'oeuvre</a>
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
