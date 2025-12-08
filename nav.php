<?php
  require_once __DIR__ . "/config.php";
?>
<nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container-fluid">
    <!-- Logo ou nom du site -->
    <a class="navbar-brand" href="./">Entreprise SARL ADRIEN GARNIER</a>
    <!-- Burger du responsive -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <!-- Tous les items de la navbar (link, home, etc...) -->
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link" href="#">Accueil</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Nos références</a>
        </li>
            <li class="nav-item">
          <a class="nav-link" href="#">Contact</a>
        </li>
            <li class="nav-item">
          <a class="nav-link" href="views/client/add_client_form.php">S'inscrire</a>
        </li>


        <?php if (isUserLoggedIn()): ?>
        <!-- C'est un drop down -->
        <li class="nav-item dropdown">
            <!-- Affichage textuel du dropdown -->
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Profil
          </a>
          <!-- Affchage de la liste du drop down -->
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#">Vos coordonées</a></li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li><a class="dropdown-item" href="#">Demander un devis</a></li>
            <li><a class="dropdown-item" href="#">Vos documents</a></li>
          </ul>
        </li>
        <!-- Déconnexion -->
        <li class="nav-item my-auto text-primary"><?= "Bonjour " . htmlentities($_SESSION["name"]) ?></li>
        <li class="nav-item">
          <a class="nav-link" href="logout.php">Déconnexion</a>
        </li>
        <?php else: ?>
        <!-- Connexion -->
        <li class="nav-item">
          <a class="nav-link" href="login.php">Connexion</a>
        </li>
        <?php endif ?>





        <?php if (isUserLoggedIn()): ?>
        <!-- C'est un drop down -->
        <li class="nav-item dropdown">
            <!-- Affichage textuel du dropdown -->
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Gestion interne
          </a>
          <!-- Affchage de la liste du drop down -->
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="views/client/client_list.php">Gestion des comptes clients</a></li>

            <li>
              <hr class="dropdown-divider">
            </li>
            <li><a class="dropdown-item" href="views/personnel/add_personnel_form.php">Ajouter du personnel</a></li>
            <li><a class="dropdown-item" href="views/personnel/personnel_list.php">Gestion du personnel</a></li>
          </ul>
        </li>
        <!-- Déconnexion -->
        <li class="nav-item my-auto text-primary"><?= "Bonjour " . htmlentities($_SESSION["name"]) ?></li>
        <li class="nav-item">
          <a class="nav-link" href="controller/auth/logout_ctrl.php">Déconnexion</a>
        </li>
        <?php else: ?>
        <!-- Connexion -->
        <li class="nav-item">
          <a class="nav-link" href="login.php">Connexion</a>
        </li>
        <?php endif ?>
      </ul>
              <?php if (isUserLoggedIn()): ?>
      <form class="d-flex input-group w-25" role="search">
        <input class="form-control" type="search" placeholder="Rechercher un client" aria-label="Search"/>
        <button class="btn btn-outline-success" type="submit"><i class="bi bi-search"></i></button>
      </form>
              <?php endif ?>
    </div>
  </div>
</nav>