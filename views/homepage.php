<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../head.php';    // head_with_title
$title = "Accueil";

// ----------------- CONTENT -----------------
$content = <<<HTML

<header class="mt-1">
   <h6 class="title border-bottom">Accueil</h6>

<div class="d-flex flex-row gap-4">
<div id="carouselExampleCaptions" class="carousel slide w-55 flex">

  <div class="carousel-indicators">
    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1" aria-label="Slide 2"></button>
    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2" aria-label="Slide 3"></button>
  </div>  
  <div class="ratio ratio-16x9">
  <div class="carousel-inner ">
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
  <button class="carousel-control-prev " type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
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
<p class="text-justify">Depuis plus de 75 années, nous accompagnons particuliers et professionnels dans tous leurs projets de construction et de rénovation. Nous combinons savoir-faire traditionnel et techniques modernes pour des réalisations solides, durables et esthétiques.</p>


            <div class="dropdown">
                <a class="btn btn2 dropdown-toggle d-flex align-items-center w-50 shadow" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="me-2 text-start">
                        <div class="text-uppercase">Demandez un devis</div>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-border  shadow">
                    <li class="p-2 me-2 text-end">
                        <a href="views/auth/add_client_form.php" class="text-white text-decoration-none"> <i class="bi bi-person-plus me-2"></i>Veuillez vous inscrire</a>
                    </li>
                </ul>
            </div>

</div>
</div>
</header>
<section class="mt-1">
   <h6 class="title border-bottom">Nos services</h6>
</section>
<section class="mt-1">
   <h6 class="title border-bottom">Nos réalisations</h6>
</section>
<section class="mt-1">
   <h6 class="title border-bottom">Nous contacter</h6>
</section>
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
