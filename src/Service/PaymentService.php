<?php

namespace App\Service;

use App\Entity\Reservation;
use Doctrine\Common\Persistence\ObjectManager;
use Stripe\Charge;
use Stripe\Error\Card;
use Stripe\Stripe;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class PaymentService {

    private $_flash;
    private $_manager;

    public function __construct(FlashBagInterface $flashBag, ObjectManager $manager){
        Stripe::setApiKey(getenv('STRIPE_SECRET'));
        $this->_flash = $flashBag;
        $this->_manager = $manager;

    }

    public function chargeOrder(Reservation $reservation, $token){
        try {
            Charge::create([
                'amount' => $reservation->getPrice() * 100,
                'currency' => 'eur',
                'description' => 'Musée du Louvre - Billeterie',
                'source' => $token
            ]);
            $this->_flash->add('notice', 'Votre paiement a fonctionné');
            $reservation->setReservationStatus(1);
            $this->_manager->persist($reservation);
            $this->_manager->flush();
        } catch (Card $e){
            $this->_flash->add('error', $e->getMessage());
        }

    }

}
