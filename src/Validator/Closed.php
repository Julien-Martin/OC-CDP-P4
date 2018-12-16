<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Class Closed
 * @package App\Validator
 */
class Closed extends Constraint {

    /**
     * @var string
     */
    public $message = "Le musée du Louvre est fermé le mardi et le dimanche";

    /**
     * @return string
     */
    public function validatedBy()
    {
        return get_class($this).'Validator';
    }

}
