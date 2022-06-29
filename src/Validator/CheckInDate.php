<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class CheckInDate extends Constraint
{
    public $message = "The check in date isn't valid";
}