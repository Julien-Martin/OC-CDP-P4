<?php

namespace App\Validator;

use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class PastDayValidator
 * @package App\Validator
 */
class PastDayValidator extends ConstraintValidator {

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        if(!$constraint instanceof PastDay){
            throw new UnexpectedTypeException($constraint, PastDay::class);
        }

        $currentDate = date_format(new \DateTime(), 'y/m/d');
        $selectedDate = date_format($value, 'y/m/d');
        if($currentDate > $selectedDate){
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }

    }
}
