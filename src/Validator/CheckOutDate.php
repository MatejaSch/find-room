<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class CheckOutDate extends Constraint
{
    public $message = "The check out date isn't valid";
}