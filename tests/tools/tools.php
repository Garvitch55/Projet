<?php

/**
 * Retourne l'âge d'une personne en fonction d'une date de naissance.
*/
function calculateAge(string $birthdate): int {
    $date = new DateTime($birthdate);
    $now = new DateTime();
    return $now->diff($date)->y;
}

/**
 * Additionne deux nombres.
*/
function add(int $a, int $b): int {
    return $a + $b;
}

function computePrice(float $price, float $discount) {
    return $price - ($price * $discount);
}