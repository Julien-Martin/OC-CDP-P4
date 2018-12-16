<?php

namespace App\Service;

/**
 * Class AgeService
 * @package App\Service
 */
class AgeService {

    /**
     * Get age with birthday and now
     * @param $birthday
     * @return int
     */
    public function getAge($birthday){
        $age = date_diff(new \DateTime(), $birthday)->y;
        return $age;
    }

}
