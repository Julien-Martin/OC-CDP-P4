<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class ClosedValidator
 * @package App\Validator
 */
class ClosedValidator extends ConstraintValidator {

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        if(!$constraint instanceof Closed){
            throw new UnexpectedTypeException($constraint, Closed::class);
        }

        $closedDays = ['Tue', 'Sun'];
        $selectedDate = date_format($value, 'D');
        foreach ($closedDays as $closedDay){
            if($selectedDate === $closedDay){
                $this->context->buildViolation($constraint->message)
                    ->addViolation();
            }
        }
    }
}
