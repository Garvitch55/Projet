<?php

require_once __DIR__ . '/../../config.php';

require_once __DIR__ . '/../../head.php';    // head_with_title
$title = "Clients";

ob_start();
require ROOT . "notification.php";
$notification = ob_get_clean();

$content = <<<HTML

<h1 class="text-center mt-3">Inscription :</h1>
<p class="text-center mt-3">Votre compte client vous servira d'avoir accès à la demande de devis en ligne.</p>

<head>
    <script src="js/api_date.js"></script>
    <script src="js/api_adresse.js" defer></script>
</head>

<!-- Action correspond à l'adresse du fichier ou les données du formulaire iront après validation -->
<form class="w-50 mx-auto mb-3" style="margin-top: 10px;" action="controller/auth/add_client_ctrl.php" method="POST">

    <div class="row">
        <!-- prénom -->
        <div class="mb-3 w-50">
            <label for="firstname" class="form-label required">Votre prénom : <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="firstname" required>
        </div>
        <!-- nom -->
        <div class="mb-3 w-50">
            <label for="lastname" class="form-label required">Votre nom : <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="lastname" required>
        </div>
    </div>

    <!-- numéro de téléphone -->
    <div class="mb-3">
        <label for="phone" class="form-label required">
            Votre numéro de téléphone : <span class="text-danger">*</span>
        </label>
        <input
            type="tel"
            name="phone"
            id="phone"
            class="form-control"
            placeholder="Entrez votre numéro de téléphone"
            required
            pattern="[0-9]{10}"
            title="Veuillez entrer un numéro à 10 chiffres">
    </div>

    <!-- adresse mail -->
    <div class="row">
        <div class="mb-3 w-50">
            <label for="email" class="form-label required">
                Votre adresse e-mail : <span class="text-danger">*</span>
            </label>
            <input
                type="email"
                name="email"
                id="email"
                class="form-control"
                placeholder="Entrez votre adresse e-mail"
                required>
        </div>
        <div class="mb-3 w-50">
            <label for="email_confirm" class="form-label required">
                Confirmer votre adresse e-mail : <span class="text-danger">*</span>
            </label>
            <input
                type="email"
                name="email_confirm"
                id="email_confirm"
                class="form-control"
                placeholder="Confirmez votre adresse e-mail"
                required>
        </div>
    </div>

<!-- Mot de passe -->
<div class="row">
    <div class="mb-3 w-50">
        <label for="password" class="form-label required">
            Mot de passe : <span class="text-danger">*</span>
        </label>
        <input
            type="password"
            name="password"
            id="password"
            class="form-control"
            required
            minlength="6"
            placeholder="Entrez votre mot de passe">
    </div>

    <div class="mb-3 w-50">
        <label for="password_confirm" class="form-label required">
            Confirmez le mot de passe : <span class="text-danger">*</span>
        </label>
        <input
            type="password"
            name="password_confirm"
            id="password_confirm"
            class="form-control"
            required
            minlength="6"
            placeholder="Confirmez votre mot de passe">
    </div>
</div>

    <!-- votre date naissance -->
    <div class="mb-3">
        <label for="birthdate" class="form-label">Votre date de naissance : </label>
        <input type="date" class="form-control" name="birthdate">
    </div>

    <!-- Adresse API-->
    <!-- Rue -->
    <div class="mb-3">
        <label for="rue" class="form-label required"> Rue : <span class="text-danger">*</span></label>
        <input type="text" class="form-control" name="rue" id="rue" placeholder="Numéro et nom de la rue" autocomplete="off" required>
        <div id="suggestions" class="suggestions"></div>
    </div>

    <div class="row">
        <!-- Code postal -->
        <div class="mb-3 w-50">
            <label for="cp" class="form-label required">
                Code postal : <span class="text-danger">*</span>
            </label>
            <input id="cp" type="text" class="form-control" name="cp"
                placeholder="Entrez votre code postal"
                required>
        </div>
        <!-- Ville -->
        <div class="mb-3 w-50">
            <label for="ville" class="form-label required">Ville : <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="ville" id="ville" placeholder="Entrez votre ville" required>
        </div>
    </div>

    <!-- Votre demande -->
    <div class="mb-3">
        <label for="demande" class="form-label required">Votre demande : <span class="text-danger">*</span></label>
        <textarea name="demande" class="form-control" style="height: 200px;" placeholder="Ecrivez nous vos besoins on vous contactera dans les plus brefs délais." cols="50" rows="10" required></textarea>
    </div>

    <!-- Bouton de validation -->
    <div class="text-center">
        <input type="submit" value="Vous inscrire" class="btn text-white" style="background:#e38f3c;"
    onmouseover="this.style.background='#41403b';"
    onmouseout="this.style.background='#e38f3c';">
    </div>
</form>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
HTML;

// ----------------- INCLURE LAYOUT -----------------
require __DIR__ . '/../../layout.php';
