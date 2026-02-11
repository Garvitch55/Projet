<?php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../head.php';    // head_with_title
$title = "Nous contacter";

// ----------------- CONTENT -----------------
$content = <<<HTML
<div class="mt-1 mb-4">
         <h6 class="title">Nous contacter</h6>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
HTML;

// ----------------- INCLURE LAYOUT -----------------
require __DIR__ . '/../layout.php';
