<?php
require __DIR__ . "/../vendor/autoload.php";

require_once __DIR__ . "/../config.php";

use PHPUnit\Framework\TestCase;

class firstLetterVowelDetectorTest extends TestCase {

    public function testStartsWithVowelsLowercase() {
        $this->assertEquals("l'", firstLetterVowelDetector('avion', 'le', 'l\''));
        $this->assertEquals("l'", firstLetterVowelDetector('éléphant', 'le', 'l\''));
        $this->assertEquals("l'", firstLetterVowelDetector('orphelinat', 'le', 'l\''));
        $this->assertEquals("l'", firstLetterVowelDetector('urine', 'le', 'l\''));
        $this->assertEquals("l'", firstLetterVowelDetector('enfant', 'le', 'l\''));
    }

    public function testStartsWithVowelsUppercase() {
        $this->assertEquals("l'", firstLetterVowelDetector('Ardèche', 'le', 'l\''));
        $this->assertEquals("l'", firstLetterVowelDetector('Écosse', 'le', 'l\''));
        $this->assertEquals("l'", firstLetterVowelDetector('Euphrate', 'le', 'l\''));
        $this->assertEquals("l'", firstLetterVowelDetector('Utrecht', 'le', 'l\''));
        $this->assertEquals("l'", firstLetterVowelDetector('Orégon', 'le', 'l\''));
    }

    public function testStartsWithConsonantLowercase() {
        $this->assertEquals("le", firstLetterVowelDetector('con', 'le', 'l\''));
        $this->assertEquals("la", firstLetterVowelDetector('grippe', 'la', 'l\''));
        $this->assertEquals("la", firstLetterVowelDetector('prolixité', 'la', 'l\''));
    }

    public function testStartsWithConsonantUppercase() {
        $this->assertEquals("la", firstLetterVowelDetector('Tour Eiffel', 'la', 'l\''));
        $this->assertEquals("le", firstLetterVowelDetector('Soudan', 'le', 'l\''));
        $this->assertEquals("le", firstLetterVowelDetector('Sphynx', 'le', 'l\''));
    }
}