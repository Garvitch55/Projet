<?php 
require_once __DIR__ . "/config.php"; 
?>

<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">

        <!-- Logo / Nom du site -->
        <a class="navbar-brand" href="./">Entreprise SARL ADRIEN GARNIER</a>

        <!-- Burger responsive -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
            aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                <!-- Navigation générale -->
                <li class="nav-item"><a class="nav-link" href="#">Accueil</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Nos références</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Contact</a></li>

                <!-- Inscription client uniquement si non connecté -->
                <?php if (!isUserLoggedIn()): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="views/client/add_client_form.php">Inscription Client</a>
                    </li>
                <?php endif; ?>

                <!-- Partie connectée -->
                <?php if (isUserLoggedIn()): ?>

                    <!-- Dropdown selon rôle -->
                    <?php $role = getUserRole(); ?>

                    <?php if ($role === "client"): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                Mon Espace Client
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Vos informations</a></li>
                                <li><a class="dropdown-item" href="#">Vos documents</a></li>
                                <li><a class="dropdown-item" href="#">Demander un devis</a></li>
                            </ul>
                        </li>
                    <?php elseif ($role === "employe"): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                Espace Employé
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Liste des clients</a></li>
                                <li><a class="dropdown-item" href="#">Créer un devis</a></li>
                            </ul>
                        </li>
                    <?php elseif ($role === "admin"): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                Administration
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="views/personnel/add_personnel_form.php">Ajouter un employé</a></li>
                                <li><a class="dropdown-item" href="views/personnel/personnel_list.php">Gestion du personnel</a></li>
                                <li><a class="dropdown-item" href="views/client/client_list.php">Gestion des clients</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>

                    <!-- Affichage nom + déconnexion -->
                    <li class="nav-item my-auto text-primary ms-2">
                        <?= "Bonjour " . htmlentities($_SESSION["name"]) ?>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="controller/auth/logout_ctrl.php">Déconnexion</a>
                    </li>

                <?php else: ?>
                    <!-- Dropdown Connexion pour non-connectés -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            Connexion
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="views/login.php?type=admin">Connexion Administrateur</a></li>
                            <li><a class="dropdown-item" href="views/login.php?type=employe">Connexion Employé</a></li>
                            <li><a class="dropdown-item" href="views/login.php?type=client">Connexion Client</a></li>
                        </ul>
                    </li>
                <?php endif; ?>

            </ul>

            <!-- Barre de recherche uniquement pour connectés -->
            <?php if (isUserLoggedIn()): ?>
                <form class="d-flex input-group w-25">
                    <input class="form-control" type="search" placeholder="Rechercher un client">
                    <button class="btn btn-outline-success"><i class="bi bi-search"></i></button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</nav>