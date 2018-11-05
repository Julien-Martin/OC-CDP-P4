<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Repository\ReservationRepository;
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
    /**
     * @Route("/billeterie/{reservationCode}", name="payment")
     * @param Request $request
     * @param ObjectManager $manager
     * @param ReservationRepository $repository
     * @param \Swift_Mailer $mailer
     * @param $reservationCode
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function payment(Request $request, ObjectManager $manager, ReservationRepository $repository, \Swift_Mailer $mailer, $reservationCode){
        $reservation = $repository->findOneBy(['reservationCode' => $reservationCode]);
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
            Stripe::setApiKey($this->getParameter('stripe_secret_key'));
            try {
                if($reservation->getReservationStatus()){
                    return $this->redirectToRoute('home');
                }
                Charge::create([
                    'amount' => $price * 100,
                    'currency' => 'eur',
                    'description' => 'Test',
                    'source' => $form->get('token')->getViewData()
                ]);
                $this->addFlash('notice', 'Your payment was successful');
                $reservation->setReservationStatus(1);
                $manager->persist($reservation);
                $manager->flush();
            } catch (Card $e){
                dump($e->getMessage());
                $this->addFlash(
                    'error',
                    $e->getMessage()
                );
            }
        }

        //Génération d'un QRCode et envoi du mail
        if($reservation->getReservationStatus() == 1){
            $qrCode = $this->createQRCode($reservation);
            $mailer->send($this->createEmail($reservation, $qrCode));
        }

        return $this->render('payment/index.html.twig', [
            'visitors' => $visitors,
            'price' => $reservation->getPrice(),
            'visitDate' => $reservation->getVisitDate()->format("d/F/Y"),
            'paymentForm' => $form->createView(),
            'stripe_public_key' => $this->getParameter('stripe_public_key')
        ]);
    }

    private function createQRCode(Reservation $reservation) {
        $qrCode = new QrCode();
        $qrCode
            ->setText($reservation->getReservationCode())
            ->setSize(300)
            ->setPadding(10)
            ->setErrorCorrection('high')
            ->setForegroundColor(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0))
            ->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0))
            ->setLabelFontSize(16)
            ->setImageType(QrCode::IMAGE_TYPE_PNG)
        ;
        return $qrCode;
    }

    private function createEmail(Reservation $reservation, QrCode $qrCode){
        $message = new \Swift_Message();
        $imgUrl = $message->embed(\Swift_Image::fromPath('img/logo.png'));
        $qrImg = $message->embed(\Swift_Image::fromPath('data:'.$qrCode->getContentType().';base64,'.$qrCode->generate()));
        $message
            ->setFrom($this->getParameter('email'))
            ->setTo($reservation->getEmail())
            ->setSubject('Billet pour le Musée du Louvre')
            ->setBody($this->renderView('payment/email.html.twig', [
                'reservation' => $reservation,
                'logo' => $imgUrl,
                'qrImg' => $qrImg
            ]), 'text/html');
        return $message;
    }
}
