<?php

namespace App\Tests\Entity;

use App\Entity\Reservation;
use App\Entity\Visitor;
use PHPUnit\Framework\TestCase;

class VisitorTest extends TestCase {

    public function testSetLastname(){
        $visitor = new Visitor();
        $value = 'nom';
        $visitor->setLastname($value);
        $this->assertEquals($value, $visitor->getLastname());
    }

    public function testSetFirstname(){
        $visitor = new Visitor();
        $value = 'prénom';
        $visitor->setFirstname($value);
        $this->assertEquals($value, $visitor->getFirstname());
    }

    public function testSetBirthdate(){
        $visitor = new Visitor();
        $value = new \DateTime();
        $visitor->setBirthdate($value);
        $this->assertEquals($value, $visitor->getBirthdate());
    }

    public function testSetNationality(){
        $visitor =  new Visitor();
        $value = 'Française';
        $visitor->setNationality($value);
        $this->assertEquals($value, $visitor->getNationality());
    }

    public function testSetReducedRate(){
        $visitor = new Visitor();
        $value = false;
        $visitor->setReducedRate($value);
        $this->assertEquals($value, $visitor->getReducedRate());
    }

    public function testSetReservation(){
        $visitor = new Visitor();
        $reservation = new Reservation();
        $visitor->setReservation($reservation);
        $this->assertEquals($reservation, $visitor->getReservation());

    }

}