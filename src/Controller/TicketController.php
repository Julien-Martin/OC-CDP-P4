<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\Visitor;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TicketController extends AbstractController
{
    /**
     * @Route("/billeterie", name="reservation")
     * @param Request $request
     * @param ObjectManager $manager
     * @param ReservationRepository $repository
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function ticket(Request $request, ObjectManager $manager, ReservationRepository $repository) {
        $reservation = new Reservation();
        $reservation->setReservationCode(bin2hex(random_bytes(20)));
        $reservation->setReservationStatus(0);
        $reservation->setReservationDate(new \DateTime());
        $outOfStockDates = $repository->findOutOfStock($this->getParameter('limit_per_day'));
        if(date('H') >= 14){
            $reservation->setHalfDay(true);
        }
        $visitor = new Visitor();
        $visitor->setReservation($reservation);

        $reservation->addVisitor($visitor);
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reservation->setPrice($this->price($reservation));
            $manager->persist($reservation);
            $manager->flush();
            return $this->redirectToRoute('payment', ['reservationCode' => $reservation->getReservationCode()]);
        }

        return $this->render('ticket/index.html.twig', [
            'form' => $form->createView(),
            'outOfStockDates' => $outOfStockDates
        ]);
    }

    /**
     * Get price with age
     * @param $reservation
     * @return int
     */
    private function price(Reservation $reservation){
        $visitorNumber = count($reservation->getVisitors());
        $price = 0;
        $halfDay = $reservation->getHalfDay();
        for ($i = 0; $i < $visitorNumber; $i++){
            $age = $this->getAge($reservation->getVisitors()[$i]->getBirthdate());
            $reducedRate = $reservation->getVisitors()[$i]->getReducedRate();
            if($age >= 12 && $age < 60 && $reducedRate == false){
                $price += $this->getParameter('rate_normal');
            } elseif ($age >= 4 && $age < 12 && $reducedRate == false){
                $price += $this->getParameter('rate_children');
            } elseif ($age >= 60 && $reducedRate == false){
                $price += $this->getParameter('rate_senior');
            } elseif($age >= 4 && $reducedRate == true){
                $price += $this->getParameter('rate_reduced');
            } elseif($age < 4) {
                $price += $this->getParameter('rate_baby');
            }
        }
        if($halfDay){
            $price *= $this->getParameter('halfDay_percentage');
        }
        return $price;
    }


    /**
     * Get age with current date and birthdate
     * @param $birthDate
     * @return int
     */
    private function getAge($birthDate){
        $age = date_diff(new \DateTime(), $birthDate)->y;
        return $age;
    }
}
