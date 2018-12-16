<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\Visitor;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use App\Service\PriceService;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TicketController extends AbstractController
{

    private $_manager;
    private $_repository;
    private $_priceService;

    public function __construct(ObjectManager $manager, ReservationRepository $repository, PriceService $priceService){
        $this->_manager = $manager;
        $this->_repository = $repository;
        $this->_priceService = $priceService;
    }

    /**
     * @Route("/billeterie", name="reservation")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function ticket(Request $request) {
        $reservation = new Reservation();
        $reservation->setReservationCode(bin2hex(random_bytes(20)));
        $reservation->setReservationStatus(0);
        $reservation->setReservationDate(new \DateTime());
        $outOfStockDates = $this->_repository->findOutOfStock($this->getParameter('limit_per_day'));
        if(date('H') >= 14){
            $reservation->setHalfDay(true);
        }
        $visitor = new Visitor();
        $visitor->setReservation($reservation);

        $reservation->addVisitor($visitor);
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reservation->setPrice($this->_priceService->computePrice($reservation));
            $this->_manager->persist($reservation);
            $this->_manager->flush();
            return $this->redirectToRoute('payment', ['reservationCode' => $reservation->getReservationCode()]);
        }

        return $this->render('ticket/index.html.twig', [
            'form' => $form->createView(),
            'outOfStockDates' => $outOfStockDates
        ]);
    }
}
