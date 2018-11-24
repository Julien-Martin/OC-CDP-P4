<?php

namespace App\Tests\Entity;

use App\Entity\Reservation;
use App\Entity\Visitor;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class ReservationTest extends TestCase {

    public function testSetReservationCode(){
        $reservation = new Reservation();
        $value = '3141592654';
        $reservation->setReservationCode($value);
        $this->assertEquals($value, $reservation->getReservationCode());
    }

    public function testSetReservationStatus(){
        $reservation = new Reservation();
        $value = true;
        $reservation->setReservationStatus($value);
        $this->assertEquals($value, $reservation->getReservationStatus());
    }

    public function testSetPrice(){
        $reservation = new Reservation();
        $value = 120;
        $reservation->setPrice($value);
        $this->assertEquals($value, $reservation->getPrice());
    }

    public function testSetReservationDate(){
        $reservation = new Reservation();
        $value = new \DateTime();
        $reservation->setReservationDate($value);
        $this->assertEquals($value, $reservation->getReservationDate());
    }

    public function testSetVisitDate(){
        $reservation = new Reservation();
        $value = new \DateTime();
        $reservation->setVisitDate($value);
        $this->assertEquals($value, $reservation->getVisitDate());
    }

    public function testSetHalfDay(){
        $reservation = new Reservation();
        $value = true;
        $reservation->setHalfDay($value);
        $this->assertEquals($value, $reservation->getHalfDay());
    }

    public function testSetEmail(){
        $reservation = new Reservation();
        $value = 'test@test.fr';
        $reservation->setEmail($value);
        $this->assertEquals($value, $reservation->getEmail());
    }

    public function testSetVisitors(){
        $reservation = new Reservation();
        $visitor = new Visitor();
        $value = new ArrayCollection();
        $value[] = $visitor;
        $reservation->addVisitor($visitor);
        $this->assertEquals($value, $reservation->getVisitors());
    }
}