<?php

namespace App\Validator;

use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ResetPasswordEmailValidator extends ConstraintValidator
{
    public function __construct(
        private UserRepository $userRepository,
    )
    {
    }

    public function validate($value, Constraint $constraint): void
    {
        $user = $this->userRepository->findOneBy(['email' => $value]);

        if(!$user) {
            $this->context->buildViolation($constraint->notExistingUser)
                ->addViolation();
        }

    }
}
