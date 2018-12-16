<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Class PastDay
 * @package App\Validator
 */
class PastDay extends Constraint {

    /**
     * @var string
     */
    public $message = "La date de visite ne peut être une date antérieure à la date actuelle.";

    /**
     * @return string
     */
    public function validatedBy()
    {
        return get_class($this).'Validator';
    }

}
