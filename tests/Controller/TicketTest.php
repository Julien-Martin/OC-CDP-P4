<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\TestCase;
use App\Entity\Reservation;
use App\Entity\Visitor;

class TicketTest extends TestCase {

    /**
     * @test
     */
    public function createVisitor(){
        $visitor = new Visitor();
        $reservation = new Reservation();

        $visitor->setLastname('MARTIN');
        $visitor->setFirstname('Julien');
        $visitor->setBirthdate(new \DateTime(1996-01-15));
        $visitor->setReducedRate(0);
        $visitor->setNationality('Française');
        $visitor->setReservation($reservation);

        $this->assertEquals($reservation, $visitor->getReservation());
    }


    /**
     * @test
     */
    public function createReservation(){
        $reservation = new Reservation();
        $reservation->setReservationStatus(0);
        $reservation->setHalfDay(1);
        $reservation->setReservationDate(new \DateTime(2018-11-11));
        $reservation->setReservationCode('3141592653598');
        $reservation->setPrice(100);
        $reservation->setEmail('reykozz15@gmail.com');
        $reservation->setVisitDate(new \DateTime(2018-11-11));

        $visitor = new Visitor();

        $visitor->setReservation($reservation);

        $this->assertEquals($reservation, $visitor->getReservation());
    }
}
