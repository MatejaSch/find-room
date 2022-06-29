<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CheckOutDateValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) to take care of that
        if (null === $value || '' === $value) {
            return;
        }

        $checkOutMinimum = date("H") > 3 ? date("Y-m-d", strtotime("+2 day")) : date("Y-m-d", strtotime("+1 day"));
        $checkOutMaximum = date("H") > 3 ? date("Y-m-d", strtotime("+1 year")) : date("Y-m-d", strtotime("+1 year -1 day"));

        $value =  date_format($value, 'Y-m-d');

        if ($value < $checkOutMinimum || $value > $checkOutMaximum) {
            // the argument must be a string or an object implementing __toString()
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value)
                ->addViolation();
        }
    }
}