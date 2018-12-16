<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Class Holiday
 * @package App\Validator
 */
class Holiday extends Constraint {

    /**
     * @var string
     */
    public $message = "Le musée du Louvre est fermé à la date sélectionné.";

    /**
     * @return string
     */
    public function validatedBy()
    {
        return get_class($this).'Validator';
    }

}
