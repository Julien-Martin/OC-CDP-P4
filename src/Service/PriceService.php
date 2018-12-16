<?php

namespace App\Service;

use App\Entity\Reservation;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Class PriceService
 * @package App\Service
 */
class PriceService {

    private $_params;
    private $_ageService;

    /**
     * PriceService constructor.
     * @param ParameterBagInterface $parameterBag
     * @param AgeService $ageService
     */
    public function __construct(ParameterBagInterface $parameterBag, AgeService $ageService){
        $this->_params = $parameterBag;
        $this->_ageService = $ageService;
    }

    /**
     * Compute price with age
     * @param Reservation $reservation
     * @return int|mixed
     */
    public function computePrice(Reservation $reservation){
        $visitorNumber = count($reservation->getVisitors());
        $price = 0;
        $halfDay = $reservation->getHalfDay();
        for ($i = 0; $i < $visitorNumber; $i++){
            $age = $this->_ageService->getAge($reservation->getVisitors()[$i]->getBirthdate());
            $reducedRate = $reservation->getVisitors()[$i]->getReducedRate();
            if($age >= 12 && $age < 60 && $reducedRate == false){
                $price += $this->_params->get('rate_normal');
            } elseif ($age >= 4 && $age < 12 && $reducedRate == false){
                $price += $this->_params->get('rate_children');
            } elseif ($age >= 60 && $reducedRate == false){
                $price += $this->_params->get('rate_senior');
            } elseif($age >= 4 && $reducedRate == true){
                $price += $this->_params->get('rate_reduced');
            } elseif($age < 4) {
                $price += $this->_params->get('rate_baby');
            }
        }
        if($halfDay){
            $price *= $this->_params->get('halfDay_percentage');
        }
        return $price;
    }
}
