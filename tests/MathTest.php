<?php

require __DIR__ . "/../vendor/autoload.php";

use PHPUnit\Framework\TestCase;

/*

Les tests unitaire en PHP

Un test unitaire est un petit programme qui vérifie automatiquement qu'une fonction marche correctement.

- On teste une seule fonction ou une seule méthode à la fois
- On donne une entrée et on vérifie que le résultat sur bien celui qu'on attend
- En cas de changement de la fonction, les tests permettent de savoir si quelque chose est cassé

C'est un outil essentiel pour éviter les bugs.

Pourquoi faire des tests ?

Les tests unitaires permettent :
    - de repérer les erreurs automatiquement
    - d'être sûr que le code continu de fonctionner après une modif
    - d'éviter les regressions.
    - de réfléchir à ce que la fonction doit vraiment faire
    - de gagner du temps sur les gros projets.

Voici les méthodes de test les plus utilisées.
assertSame($la_valeur_attendue, la_valeur_obtenu_par_la_fonction_testée)
Le résultat doit être le même

assertEqual()
assertTrue() ou assertFalse()
assertEmpty() ou assertNotEmpty()
assertCount($nombre d'élément, le tableau à tester)

*/

require_once __DIR__ . "/tools/tools.php";

class MathTest extends TestCase {
    
    // toujours commencer sa méthode par test
    // ensuite le nom de la fonction
    // et le nom de ce qu'on test
    public function testAdditionAdd() {
        $this->assertSame(4, add(2,2));
    }

    public function testDiscountPrice() {
        $this->assertSame(400.0, computePrice(500.0, 0.2));
    }

    public function testNoDiscountPrice() {
        $this->assertSame(100.0, computePrice(100.0, 0.0));
    }

    public function testZeroPrice() {
        $this->assertSame(0.0, computePrice(0.0, 0.5));
    }

    public function testDiscountLargerThanOne() {
        $this->assertSame(-80.0, computePrice(400.0, 1.2));
    }

    public function testNegativeEqualWithString() {
        $e = "nerd";
        $a = "Nerd";
        $this->assertEquals($e, $a, 'La valeur $a n\'est pas égale à la valeur attendue');
    }
}