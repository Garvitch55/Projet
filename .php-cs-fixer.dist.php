<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in(__DIR__)           // racine du projet
    ->exclude([
        'vendor',           // ne jamais toucher aux packages
        'assets',           // images, CSS, JS
        'views'             // HTML / HEREDOC, pour ne pas casser le format
    ])
    ->name('*.php');        // seulement les fichiers PHP

return (new Config())
    ->setRiskyAllowed(false) // false pour éviter les règles risquées
    ->setRules([
        '@PER-CS' => true,
        'array_syntax' => ['syntax' => 'short'],
        'no_unused_imports' => true,
        'single_quote' => true,
        'binary_operator_spaces' => ['default' => 'single_space'],
    ])
    ->setFinder($finder);
