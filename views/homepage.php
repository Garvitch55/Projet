<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../head.php';    // head_with_title
$title = "Accueil";

// ----------------- CONTENT -----------------
$content = <<<HTML
<div class="d-flex flex-column gap-2">
<header class="mt-1 mb-4">
   <h2 class="title mb-3">Accueil</h2>

   <div class="d-flex flex-column flex-md-row gap-4">
      <div id="carouselExampleCaptions" class="carousel slide w-55 flex">
         <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2" aria-label="Slide 3"></button>
         </div> 
         <div class="ratio ratio-16x9">
            <div class="carousel-inner">
               <div class="carousel-item active">
                  <img src="assets/statics/images/entreprise-facade.jpg" class="d-block w-100" alt="...">
                  <div class="carousel-caption carousel-caption-bg d-none d-md-block">
                     <h5>Notre entreprise</h5>
                     <p>Entrée principale/accueil.</p>
                  </div>
               </div>
               <div class="carousel-item">
                  <img src="assets/statics/images/entreprise-depots-cours.jpg" class="d-block w-100" alt="...">
                  <div class="carousel-caption carousel-caption-bg d-none d-md-block">
                     <h5>Notre dépôt matériaux</h5>
                     <p>Dépôt de matériaux dans notre cours arrière.</p>
                  </div>
               </div>
               <div class="carousel-item">
                  <img src="assets/statics/images/entreprise-depots-cours.jpg" class="d-block w-100" alt="...">
                  <div class="carousel-caption carousel-caption-bg d-none d-md-block">
                     <h5>Notre dépôt matériel</h5>
                     <p>Dépôt de matériel dans notre entrepôt.</p>
                  </div>
               </div>
            </div>
         </div>
         <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
            <span class="carousel-control-prev-icon carousel-caption-bg rounded-circle w-50 shadow" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
         </button>
         <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
            <span class="carousel-control-next-icon carousel-caption-bg rounded-circle w-50 shadow" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
         </button>
      </div>

      <div class="flex w-45">
         <h6 class="text-orange-fonce fw-bold text-decoration-underline mb-4">Votre partenaire en maçonnerie de confiance :</h6>
         <p class="text-justify">Spécialistes du gros œuvre, nous accompagnons particuliers et professionnels dans leurs projets de construction et de rénovation. De la fondation à l’élévation des structures, notre équipe met son savoir-faire au service de chantiers solides, durables et conformes aux normes en vigueur.</p>

      </div>
   </div>
</header>

<section class="mt-1 mb-4">
   <div class="d-flex justify-content-between align-items-stretch">
      <h2 class="title m-0">Nos services</h2>

      <a href="views/service.php" class="btn4 d-flex align-items-center px-3">
         Voir nos services
      </a>
   </div>
   <div class="container-fluid w-100 p-0">
      <div class="row g-3 w-100 m-0">
         <div class="col-md-4 p-0">
            <div class="service-item position-relative">
               <a href="views/service.php" class="text-decoration-none">
            <h6 class="carousel-caption-bg2 p-2 mb-0">Construction neuve</h6>
            <img src="assets/statics/images/service-construction-neuve.png" alt="" class="w-100">
                  <ul class="overlay-list position-absolute top-0 start-0 d-flex flex-column justify-content-start align-items-center text-white">
               <li>Terrassement et fondations</li>
               <li>Dalles et planchers béton</li>
               <li>Élévation de murs porteurs</li>
               <li>Poteaux, poutres et voiles béton</li>
            </ul>
            </div>
            </a>
         </div>
         <div class="col-md-4 p-0">
            <div class="service-item position-relative">
            <a href="views/service.php" class="text-decoration-none">
            <h6 class="carousel-caption-bg p-2 mb-0">Rénovation & structure</h6>
            <img src="assets/statics/images/service-renovation.png" alt="" class="w-100">
                  <ul class="overlay-list position-absolute top-0 start-0 d-flex flex-column justify-content-start align-items-center text-white">
               <li>Reprise en sous-œuvre</li>
               <li>Ouvertures de murs porteurs</li>
               <li>Renforcement de structures existantes</li>
               <li>Démolition partielle liée au gros œuvre</li>
            </ul>
            </div>
         </a>
         </div>
         <div class="col-md-4 p-0">   
            <div class="service-item position-relative">
               <a href="views/service.php" class="text-decoration-none">
            <h6 class="carousel-caption-bg2 p-2 mb-0">Études & suivi de chantier</h6>
            <img src="assets/statics/images/etude.jpg" alt="" class="w-120" >
               <ul class="overlay-list position-absolute top-0 start-0 d-flex flex-column justify-content-start align-items-center text-white">
                  <li>Lecture de plans et études techniques</li>
                  <li>Implantation et préparation de chantier</li>
                  <li>Coordination du gros œuvre</li>
                  <li>Respect des normes (DTU, sécurité)</li>
               </ul>
  
               </a>
            </div>
         </div>
                  </div>
   </div>
</section>

<section class="mt-1 mb-4">
      <div class="d-flex justify-content-between align-items-stretch mb-3">
      <h2 class="title m-0">Nos réalisations</h2>

      <a href="views/reference.php" class="btn4 d-flex align-items-center px-3">
         Voir nos réalisations
      </a>
   </div>

<div class="d-flex flex-column flex-md-row">
   <div class="w-100 reference-item position-relative">
      <a href="views/reference.php" class="text-decoration-none" >
         <h6 class="carousel-caption-bg2 p-2 m-0">Secteur privé</h6>
         <div class="d-flex flex-column flex-md-row bg-gris-opacity">
            <ul class="order-2 order-md-1 w-100 text-white p-3 m-0">
               <li>Maisons indivduelles</li>
               <li>Piscines</li>
               <li>Renforcements de structures</li>
               <li>Démolitions</li>
            </ul>
            <div class="order-1 order-md-2 w-100 overflow-hidden">
               <img src="assets/statics/images/reference-secteur-prive.png" alt="" class="h-100">
            </div>
         </div>
      </a>
   </div>
  
   <div class="w-100 reference-item position-relative">
      <a href="views/reference.php" class="text-decoration-none" >
      <h6 class="carousel-caption-bg p-2 m-0">Secteur public</h6>
     <div class="d-flex flex-column flex-md-row bg-gris-opacity">
       <ul class="order-2 order-md-1 w-100 text-white p-3 m-0">
              <li>Ecoles primaire</li>
               <li>Maisons de santé</li>
               <li>Magasins</li>
               <li>Démolitions</li>
            </ul>
<div class="order-1 order-md-2 w-100 overflow-hidden">
   <img src="assets/statics/images/reference-secteur-public1.png" alt="" class="h-100">
</div>

</div>
</a>
   </div>
</div>
</section>

<section class="mt-1 mb-4">
    <div class="d-flex justify-content-between align-items-stretch mb-3">
        <h2 class="title m-0">Nous contacter</h2>
        <a href="views/contact.php" class="btn4 d-flex align-items-center px-3">
            Voir nos coordonnées
        </a>
    </div>

    <div class="d-flex flex-column flex-md-row gap-4">
        <div class="flex-md-2" style="flex: 2;">
            <h6 class="fw-bold text-orange-fonce text-decoration-underline mb-2">Adresse :</h6>
            <p class="pb-2">15 Allée du Pré l'évêque<br>55100 VERDUN, France</p>

            <h6 class="fw-bold text-orange-fonce text-decoration-underline mb-2">Téléphone :</h6>
            <p class="pb-2">+33 3 29 45 67 89</p>

            <h6 class="fw-bold text-orange-fonce text-decoration-underline mb-2">Email :</h6>
            <p class="pb-2">contact@agarnierconstruction.com</p>
        </div>

        <div class="flex-md-2" style="flex: 2;">
            <h6 class="fw-bold text-orange-fonce text-decoration-underline mb-2">Horaires :</h6>
            <ul class="list-unstyled  pb-4">
                <li>Lundi - Vendredi : 8h00 - 12h et 14h - 18h00</li>
                <li>Samedi : Fermé</li>
                <li>Dimanche : Fermé</li>
                <li>Jours fériés : Fermé</li>
            </ul>

            <h6 class="fw-bold text-orange-fonce text-decoration-underline mb-2">Réseaux sociaux :</h6>
            <p>
                <a href="#" class="text-decoration-none text-gris-fonce me-2"><i class="bi bi-facebook"></i> Facebook</a>
                <a href="#" class="text-decoration-none text-gris-fonce me-2"><i class="bi bi-instagram"></i> Instagram</a>
                <a href="#" class="text-decoration-none text-gris-fonce "><i class="bi bi-linkedin"></i> LinkedIn</a>
            </p>
        </div>
                <!-- Carte Google Maps à droite -->
        <div class="flex-md-2" style="flex: 2;">
            <h6 class="fw-bold text-orange-fonce text-decoration-underline mb-2">Notre localisation :</h6>
            <div class="ratio ratio-16x9 rounded shadow">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2621.2345678901234!2d5.3856789!3d49.1578901!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47f4456789012345%3A0xabcdef1234567890!2s15%20All%C3%A9e%20du%20Pr%C3%A9%20l'%C3%A9v%C3%AAque%2C%2055100%20Verdun%2C%20France!5e0!3m2!1sfr!2sfr!4v1699999999999!5m2!1sfr!2sfr" 
                    class="border-orange-fonce rounded-2" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>
    </div>

</section>
</div>
<script>
var myCarousel = document.querySelector('#carouselExampleCaptions');
var carousel = new bootstrap.Carousel(myCarousel, {
   interval: 3000,  // 3s
   ride: 'carousel'
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
HTML;

// ----------------- INCLURE LAYOUT -----------------
require __DIR__ . '/../layout.php';

