<?php

namespace App\Validator;

use App\ApiResource\UserApi;
use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class StudentDataValidator extends ConstraintValidator
{
    public function __construct(
        private UserRepository $userRepository,
    )
    {
    }

    public function validate($value, Constraint $constraint)
    {
        assert($value instanceof UserApi);

        $user = $this->userRepository->findOneBy(['id' => $value->getId()]);

        if (!$user) {
            $this->context->buildViolation($constraint->notExistingUser)
                ->addViolation();
            return;
        }

        if ($user->getStudent()) {
            $this->context->buildViolation($constraint->alreadyStudent)
                ->addViolation();
        }

    }
}
