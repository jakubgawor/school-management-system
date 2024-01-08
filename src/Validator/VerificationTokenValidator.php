<?php

namespace App\Validator;

use App\Repository\UserVerificationTokenRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class VerificationTokenValidator extends ConstraintValidator
{
    public function __construct(
        private UserVerificationTokenRepository $tokenRepository,
    )
    {
    }

    public function validate($value, Constraint $constraint): void
    {
        $token = $this->tokenRepository->findOneBy(['token' => $value]);

        if (!$token) {
            $this->context->buildViolation($constraint->tokenDoesNotExists)
                ->addViolation();

            return;
        }

        if ($token->getExpiresAt() < new \DateTimeImmutable()) {
            $this->context->buildViolation($constraint->expiredMessage)
                ->addViolation();
        }
    }
}
