<?php

namespace App\Validator;

use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class EmailResendVerifyValidator extends ConstraintValidator
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function validate($value, Constraint $constraint)
    {
        /* @var App\Validator\EmailResendVerify $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        /*
         *  Checks if user email is registered and not yet verified
         */
        $notVerifiedUser = $this->userRepository->findOneBy(['email' => $value, 'is_verified' => false]);

        if (null === $notVerifiedUser) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
