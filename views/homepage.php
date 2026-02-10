<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../head.php';    // head_with_title
$title = "Accueil";

// ----------------- CONTENT -----------------
$content = <<<HTML

<header class="mt-1">
   <h6 class="title border-bottom">Accueil</h6>

<div class="d-flex flex-row">
<div id="carouselExampleCaptions" class="carousel slide w-55 flex">
  <div class="carousel-indicators">
    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1" aria-label="Slide 2"></button>
    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2" aria-label="Slide 3"></button>
  </div>
  <div class="carousel-inner">
    <div class="carousel-item active">
      <img src="assets/statics/images/entreprise-facade.jpg" class="d-block w-100" alt="...">
      <div class="carousel-caption carousel-caption-bg d-none d-md-block rounded">
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
  <button class="carousel-control-prev " type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
    <span class="carousel-control-prev-icon carousel-caption-bg rounded-circle w-50 shadow" aria-hidden="true"></span>
    <span class="visually-hidden">Previous</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
    <span class="carousel-control-next-icon carousel-caption-bg rounded-circle w-50 shadow" aria-hidden="true"></span>
    <span class="visually-hidden">Next</span>
  </button>
</div>


<div class="flex">
<p>fctycyryxc</p>
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
