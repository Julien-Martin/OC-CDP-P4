<?php

namespace App\Controller;

use App\Form\PaymentType;
use App\Repository\ReservationRepository;
use App\Service\MailService;
use App\Service\PriceService;
use App\Service\QRCodeService;
use App\Service\PaymentService;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PaymentController
 * @package App\Controller
 */
class PaymentController extends AbstractController
{
    /**
     * @var ObjectManager
     */
    private $_manager;
    /**
     * @var ReservationRepository
     */
    private $_repository;
    /**
     * @var QRCodeService
     */
    private $_qrcodeService;
    /**
     * @var MailService
     */
    private $_mailService;
    /**
     * @var PaymentService
     */
    private $_paymentService;
    /**
     * @var PriceService
     */
    private $_priceService;

    /**
     * PaymentController constructor.
     * @param ObjectManager $objectManager
     * @param ReservationRepository $repository
     * @param QRCodeService $QRCodeService
     * @param MailService $mailService
     * @param PaymentService $paymentService
     * @param PriceService $priceService
     */
    public function __construct(ObjectManager $objectManager, ReservationRepository $repository, QRCodeService $QRCodeService, MailService $mailService, PaymentService $paymentService, PriceService $priceService){
        $this->_manager = $objectManager;
        $this->_repository = $repository;
        $this->_qrcodeService = $QRCodeService;
        $this->_mailService = $mailService;
        $this->_paymentService = $paymentService;
        $this->_priceService = $priceService;
    }

    /**
     * @Route("/billeterie/{reservationCode}", name="payment")
     * @param Request $request
     * @param $reservationCode
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function payment(Request $request, $reservationCode){
        $this->get('session')->getFlashBag();
        $reservation = $this->_repository->findOneBy(['reservationCode' => $reservationCode]);
        if($reservation == null){
            return $this->redirectToRoute('home');
        }
        $visitors = $reservation->getVisitors();
        $visitorsPrice = [];
        foreach ($visitors as $visitor){
            $visitorsPrice[] = $this->_priceService->computePrice($visitor, $reservation);
        }
        $form = $this->createForm(PaymentType::class);
        $form->handleRequest($request);

        //Procéder au paiement
        if($form->isSubmitted() && $form->isValid()){
            if($reservation->getReservationStatus()){
                return $this->redirectToRoute('home');
            } else {
                $this->_paymentService->chargeOrder($reservation, $form->get('token')->getViewData());
            }
        }

        //Génération d'un QRCode et envoi du mail
        if($reservation->getReservationStatus() == 1){
            $this->_mailService->sendMail($this->_mailService->createMail($reservation));
        }

        return $this->render('payment/index.html.twig', [
            'visitors' => $visitors,
            'visitorsPrice' => $visitorsPrice,
            'mail' => $reservation->getEmail(),
            'price' => $reservation->getPrice(),
            'visitDate' => $reservation->getVisitDate()->format("d/F/Y"),
            'paymentForm' => $form->createView(),
            'stripe_public_key' => getenv('STRIPE_PUBLIC')
        ]);
    }
}
