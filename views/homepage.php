<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../head.php';    // head_with_title
$title = "Accueil";

// ----------------- CONTENT -----------------
$content = <<<HTML
<div class="d-flex flex-column gap-2">
<header class="mt-1">
   <h6 class="title mb-3">Accueil</h6>

   <div class="d-flex flex-row gap-4">
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
         <h6 class="text-orange-fonce fw-bold text-decoration-underline mb-4 mt-4">Votre partenaire en maçonnerie de confiance :</h6>
         <p class="text-justify">Spécialistes du gros œuvre, nous accompagnons particuliers et professionnels dans leurs projets de construction et de rénovation. De la fondation à l’élévation des structures, notre équipe met son savoir-faire au service de chantiers solides, durables et conformes aux normes en vigueur.</p>

         <div class="dropdown">
            <a class="btn btn2 dropdown-toggle d-flex align-items-center w-50 shadow" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
               <div class="me-2 text-start">
                  <div class="text-uppercase">Demandez un devis</div>
               </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-border shadow">
               <li class="p-2 me-2 text-end">
                  <a href="views/auth/add_client_form.php" class="text-white text-decoration-none"><i class="bi bi-person-plus me-2"></i>Veuillez vous inscrire</a>
               </li>
            </ul>
         </div>
      </div>
   </div>
</header>

<section class="mt-1">
   <h6 class="title m-0">Nos services : Gros Oeuvre</h6>
   <div class="container p-0">
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
            <img src="assets/statics/images/etude-suivi.webp" alt="" class="w-100">
               <ul class="overlay-list position-absolute top-0 start-0 d-flex flex-column justify-content-start align-items-center text-white">
                  <li>Lecture de plans et études techniques</li>
                  <li>Implantation et préparation de chantier</li>
                  <li>Coordination du gros œuvre</li>
                  <li>Respect des normes (DTU, sécurité)</li>
               </ul>
               </div>
               </a>
         </div>
      </div>
   </div>
</section>

<section class="mt-1">
   <h6 class="title border-bottom">Nos réalisations</h6>
<div class="d-flex flex-row">
   <div class="w-50 reference-item position-relative">
      <a href="" class="text-decoration-none" >
         <h6 class="carousel-caption-bg2 p-2 m-0">Secteur privé</h6>
         <div class="d-flex bg-gris-opacity">
            <ul class="w-50 text-white p-3 m-0">
               <li>Maisons indivduelles</li>
               <li>Piscines</li>
               <li>Renforcements de structures</li>
               <li>Démolitions</li>
            </ul>
            <div class="w-50">
               <img src="assets/statics/images/reference-secteur-prive.png" alt="">
            </div>
         </div>
      </a>
   </div>
  
   <div class="w-50 reference-item position-relative">
      <a href="" class="text-decoration-none" >
      <h6 class="carousel-caption-bg p-2 m-0">Secteur public</h6>
     <div class="d-flex bg-gris-opacity">
       <ul class="w-50 text-white p-3 m-0">
              <li>Ecoles primaire</li>
               <li>Maisons de santé</li>
               <li>Magasins</li>
               <li>Démolitions</li>
            </ul>
       <div class="w-50">
   <img src="assets/statics/images/reference-secteur-public.png" alt="" class="w-100">
   </div>

</div>
</a>


   </div>
</div>
</section>

<section class="mt-1">
   <h6 class="title border-bottom">Nous contacter</h6>
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

