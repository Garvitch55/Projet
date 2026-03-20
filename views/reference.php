<?php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../head.php';

$title = "Nos réalisations";

$pdo = getPDO();

$stmt = $pdo->query("SELECT * FROM reference_management ORDER BY created_at DESC");
$references = $stmt->fetchAll(PDO::FETCH_ASSOC);

$cards = '';

foreach ($references as $ref) {
$cards .= '
<div class="col-12 col-sm-6 col-md-6 col-lg-4 mb-4">
    <div class="card h-100 card-references">
        
        <img src="/projet/assets/statics/images/'.($ref['image']).'" class="card-img-top rounded-1" alt="'.($ref['name']).'">
        
        <div class="card-body">
            <h5 class="card-title text-gris-fonce">'.($ref['name']).'</h5>
            
            <p class="card-text">
                Lieu: '.($ref['site']).'<br>
                Description: '.nl2br($ref['description']).'<br>
                Date de réalisation : '.($ref['Completion_date']).'<br>
            </p>
            
            <a href="views/reference_detail.php?id='.($ref['id']).'" class="btn text-white">
                Voir plus
            </a>
        </div>
        
    </div>
</div>
';
}

// ----------------- CONTENT -----------------
$content = <<<HTML

<div class="mt-1 mb-3">
      <h2 class="title">Nos réalisations</h2>
</div>

<div class="container-fluid">
    <div class="row">
        $cards
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

HTML;

// ----------------- INCLURE LAYOUT -----------------
require __DIR__ . '/../layout.php';
