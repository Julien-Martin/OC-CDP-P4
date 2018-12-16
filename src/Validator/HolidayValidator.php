<?php

namespace App\Validator;

use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class HolidayValidator
 * @package App\Validator
 */
class HolidayValidator extends ConstraintValidator {

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        if(!$constraint instanceof Holiday){
            throw new UnexpectedTypeException($constraint, Holiday::class);
        }

        $holidayDates = ['01/05', '01/11', '25/12'];
        $selectedDate = date_format($value, 'd/m');
        foreach ($holidayDates as $holidayDate){
            if($selectedDate === $holidayDate){
                $this->context->buildViolation($constraint->message)
                    ->addViolation();
            }
        }
    }
}
