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
<div class="col-md-4 mb-4">
    <div class="card h-100 card-references">
        
        <img src="/projet/images/'.($ref['image']).'" class="card-img-top rounded-0" alt="'.($ref['name']).'">
        
        <div class="card-body">
            <h5 class="card-title">'.($ref['name']).'</h5>
            
            <p class="card-text">
                Lieu: '.($ref['site']).'<br>
                Description: '.nl2br($ref['description']).'<br>
                Date de réalisation : '.($ref['Completion_date']).'<br>
            </p>
            
            <a href="reference_detail.php?id='.($ref['id']).'" class="btn text-white">
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
      <h6 class="title">Nos réalisations</h6>
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
