<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Repository\ReservationRepository;
use App\Service\MailService;
use App\Service\QRCodeService;
use App\Service\PaymentService;
use CodeItNow\BarcodeBundle\Utils\QrCode;
use Doctrine\Common\Persistence\ObjectManager;
use Stripe\Charge;
use Stripe\Error\Card;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;

class PaymentController extends AbstractController
{

    private $_manager;
    private $_repository;
    private $_qrcodeService;
    private $_mailService;
    private $_paymentService;

    public function __construct(ObjectManager $objectManager, ReservationRepository $repository, QRCodeService $QRCodeService, MailService $mailService, PaymentService $paymentService){
        $this->_manager = $objectManager;
        $this->_repository = $repository;
        $this->_qrcodeService = $QRCodeService;
        $this->_mailService = $mailService;
        $this->_paymentService = $paymentService;
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
        $price = $reservation->getPrice();
        $visitors = $reservation->getVisitors();

        $form = $this->createFormBuilder()
            ->add('token', HiddenType::class, [
                'constraints' => [new NotBlank()],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Payer',
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ])
            ->getForm();
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
            'mail' => $reservation->getEmail(),
            'price' => $reservation->getPrice(),
            'visitDate' => $reservation->getVisitDate()->format("d/F/Y"),
            'paymentForm' => $form->createView(),
            'stripe_public_key' => getenv('STRIPE_PUBLIC')
        ]);
    }
}
