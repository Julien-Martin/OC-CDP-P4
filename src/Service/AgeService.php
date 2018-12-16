<?php

namespace App\Service;

/**
 * Class AgeService
 * @package App\Service
 */
class AgeService {

    /**
     * Get age
     * @param $birthday
     * @return int
     */
    public function getAge($birthday){
        $age = date_diff(new \DateTime(), $birthday)->y;
        return $age;
    }

}
