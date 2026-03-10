<?php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../head.php';
$title = "Nous contacter";

// ----------------- Capture de la notification -----------------
ob_start();
require ROOT . "notification.php";
$notification = ob_get_clean();

// ----------------- CONTENT -----------------
$content = <<<HTML
<div class="mt-1 mb-4">
    <h2 class="title">Nous contacter</h2>
</div>

<section class="mt-4 mb-5">
    <div class="container">

        <div class="row g-4">
            <div class="contact-card bg-orange-opacity rounded-1 text-white d-flex align-items-center justify-content-center py-4">
                <p class="m-0 text-center">
                    Besoin d'aide? Ou vous avez besoin d'une demande de renseignements ou de devis? N'hésitez pas à nous envoyer un message via le formulaire, ou à nous contacter aux coordonnées ci-dessous.
                </p>
            </div>
                
            <!-- Informations de contact -->
            <div class="col-md-6">
                <h6 class="fw-bold text-orange-fonce text-decoration-underline mb-2">Notre adresse :</h6>
                <p>15 Allée du Pré l'évêque<br>55100 VERDUN, France</p>

                <h6 class="fw-bold text-orange-fonce text-decoration-underline mb-2">Notre téléphone :</h6>
                <p>+33 3 29 45 67 89</p>

                <h6 class="fw-bold text-orange-fonce text-decoration-underline mb-2">Email :</h6>
                <p>contact@agarnierconstruction.com</p>

                <h6 class="fw-bold text-orange-fonce text-decoration-underline mb-2">Horaires :</h6>
                <ul class="list-unstyled">
                    <li>Lundi - Vendredi : 8h00 - 12h00 et 14h00 - 18h00</li>
                    <li>Samedi : Fermé</li>
                    <li>Dimanche : Fermé</li>
                    <li>Jours fériés : Fermé</li>
                </ul>

                <h6 class="fw-bold text-orange-fonce text-decoration-underline mb-2">Réseaux sociaux</h6>
                <p>
                    <a href="#" class="text-decoration-none text-gris-fonce me-2"><i class="bi bi-facebook"></i> Facebook</a>
                    <a href="#" class="text-decoration-none text-gris-fonce me-2"><i class="bi bi-instagram"></i> Instagram</a>
                    <a href="#" class="text-decoration-none text-gris-fonce"><i class="bi bi-linkedin"></i> LinkedIn</a>
                </p>
            </div>

            <!-- Carte Google Maps -->
            <div class="col-md-6">
                <h6 class="fw-bold text-orange-fonce text-decoration-underline mb-3">Notre localisation :</h6>
                <div class="ratio ratio-16x9 rounded shadow">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2621.2345678901234!2d5.3856789!3d49.1578901!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47f4456789012345%3A0xabcdef1234567890!2s15%20All%C3%A9e%20du%20Pr%C3%A9%20l'%C3%A9v%C3%AAque%2C%2055100%20Verdun%2C%20France!5e0!3m2!1sfr!2sfr!4v1699999999999!5m2!1sfr!2sfr" 
                        class="border-orange-fonce rounded-2" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        </div>

        <!-- Formulaire de contact en dessous -->
        <div class="row mt-5">
            <div class="col-md-12">
                <h6 class="fw-bold text-orange-fonce text-decoration-underline mb-3">Nous envoyer un message :</h6>
               <form action="controller/ContactController.php" method="POST">
    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <label for="prenom" class="form-label">Prénom: <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="prenom" name="prenom">
        </div>
        <div class="col-md-6">
            <label for="nom" class="form-label">Nom: <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="nom" name="nom">
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <label for="email" class="form-label">Email: <span class="text-danger">*</span></label>
            <input type="email" class="form-control" id="email" name="email">
        </div>
        <div class="col-md-6">
            <label for="phone" class="form-label">Téléphone: <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="phone" name="phone">
        </div>
    </div>

    <div class="mb-3">
        <label for="subject" class="form-label">Sujet:  <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="subject" name="subject">
    </div>

    <div class="mb-3">
        <label for="message" class="form-label">Message:  <span class="text-danger">*</span></label>
        <textarea class="form-control" id="message" name="message" rows="5"></textarea>
    </div>

    <!-- Honeypot pour anti-spam (champ caché) -->
    <input type="text" name="website" style="display:none">

    <button type="submit" class="btn btn-orange-fonce text-white">Envoyer</button>

</form>
            </div>
        </div>

    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
HTML;

// ----------------- INCLURE LAYOUT -----------------
require __DIR__ . '/../layout.php';
