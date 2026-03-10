<?php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../head.php';    // head_with_title
$title = "Nous contacter";

// ----------------- CONTENT -----------------
$content = <<<HTML
<div class="mt-1 mb-4">
            <h2 class="title">Nos services & nos certifications et qualifications</h2>
</div>
<section class="mt-4 mb-5">

               <h3 class="text-orange-fonce mb-4 text-decoration-underline">Nos services :</h3>
   <div class="container">

      <p class="mb-5">
         Nous accompagnons les particuliers, les entreprises et les collectivités
         dans leurs projets de construction, de rénovation et de renforcement
         de structures. Notre équipe met son expertise technique au service
         de chaque projet pour garantir qualité, sécurité, durabilité et de haute qualité energétique.
      </p>

      <div class="row g-4">

         <!-- Service 1 -->
 <div class="col-md-6">
   <div class="service-card rounded-1 position-relative overflow-hidden">

      <img src="assets/statics/images/istockphoto-1070686006-2048x2048.webp" class="service-bg w-100" alt="">

      <div class="service-content text-white position-absolute p-4 top-0">
         <h5 class="text-gris-fonce">Études et conception</h5>

         <p>
            Nous réalisons des études techniques complètes afin d'assurer
            la faisabilité et la solidité de vos projets.
         </p>

         <ul>
            <li>Études de structure</li>
            <li>Plans techniques</li>
            <li>Analyse de faisabilité</li>
            <li>Conseil en ingénierie</li>
         </ul>
      </div>

   </div>
</div>

         <!-- Service 2 -->
         <div class="col-md-6">
   <div class="service-card rounded-1 position-relative overflow-hidden">
               <img src="assets/statics/images/istockphoto-1130573924-2048x2048.webp" class="service-bg w-100" alt="">
<div class="service-content text-white position-absolute p-4 top-0">
               <h5 class="text-gris-fonce">Travaux de construction</h5>

               <p>
                  Nous réalisons différents travaux de construction et
                  d’aménagement pour les secteurs privé et public.
               </p>

               <ul>
                  <li>Maisons individuelles</li>
                  <li>Structures béton</li>
                  <li>Extensions de bâtiments</li>
                  <li>Aménagements extérieurs</li>
               </ul>
            </div>
         </div>
</div>
         <!-- Service 3 -->
         <div class="col-md-6">
   <div class="service-card rounded-1 position-relative overflow-hidden">
               <img src="assets/statics/images/renforcement-de-structure1.jpg" class="service-bg w-100" alt="">
<div class="service-content text-white position-absolute p-4 top-0">
               <h5 class="text-gris-fonce">Renforcement de structures</h5>

               <p>
                  Nous intervenons pour renforcer et sécuriser des structures
                  existantes afin d'améliorer leur résistance et leur durabilité.
               </p>

               <ul>
                  <li>Renforcement béton</li>
                  <li>Reprise de fondations</li>
                  <li>Consolidation de structures</li>
                  <li>Diagnostic structurel</li>
               </ul>
            </div>
         </div>
</div>
         <!-- Service 4 -->
         <div class="col-md-6">
   <div class="service-card rounded-1 position-relative overflow-hidden">
               <img src="assets/statics/images/demolition-maison-individuelle-ancienne.jpg" class="service-bg w-100" alt="">
<div class="service-content text-white position-absolute p-4 top-0">

               <h5 class="text-gris-fonce">Démolition</h5>

               <p>
                  Nous réalisons des travaux de démolition dans le respect
                  des normes de sécurité et de l’environnement.
               </p>
               <ul>
                  <li>Démolition de bâtiments</li>
                  <li>Déconstruction partielle</li>
                  <li>Évacuation des gravats</li>
                  <li>Préparation du terrain</li>
               </ul>
            </div>
         </div>

      </div>
      </div>
   </div>

</section>
<section class="mt-5 mb-5">

   <div class="container">

      <h3 class="text-orange-fonce mb-4 text-decoration-underline">Certifications et qualifications :</h3>

      <p class="mb-4">
         Notre entreprise dispose de plusieurs certifications et qualifications
         garantissant la qualité de nos prestations et le respect des normes
         en vigueur dans le secteur du bâtiment et du génie civil.
      </p>

      <div class="row text-center g-4">

         <!-- Certification 1 -->
         <div class="col-md-3 col-6">
            <div class="certification-card p-3">
               <img src="assets/statics/images/Logo_qualibat.jpg" class="img-fluid mb-2" alt="Qualibat">
               <h6>QUALIBAT</h6>
               <p class="small">Certification de qualification professionnelle dans le bâtiment.</p>
            </div>
         </div>

         <!-- Certification 2 -->
         <div class="col-md-3 col-6">
            <div class="certification-card p-3">
               <img src="assets/statics/images/logo-rge.png" class="img-fluid mb-2" alt="RGE">
               <h6>RGE</h6>
               <p class="small">Reconnu Garant de l'Environnement.</p>
            </div>
         </div>

         <!-- Certification 3 -->
         <div class="col-md-3 col-6">
            <div class="certification-card p-3">
               <img src="assets/statics/images/logo-norme.webp" class="img-fluid mb-2" alt="">
               <h6>Normes de sécurité</h6>
               <p class="small">Respect strict des normes et réglementations en vigueur.</p>
            </div>
         </div>

         <!-- Certification 4 -->
         <div class="col-md-3 col-6">
            <div class="certification-card p-3">
               <img src="assets/statics/images/logo-experience.webp" class="img-fluid mb-2" alt="">
               <h6>Expérience</h6>
               <p class="small">Plusieurs années d’expérience dans les travaux de structure.</p>
            </div>
         </div>

      </div>

   </div>

</section>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
HTML;

// ----------------- INCLURE LAYOUT -----------------
require __DIR__ . '/../layout.php';
