<?php

namespace App\Validator;

use App\Entity\User;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UserRoleExistenceValidator extends ConstraintValidator
{
    public function __construct()
    {
    }

    public function validate($value, Constraint $constraint): void
    {
        assert($value instanceof User);
        if ($value->getStudent() || $value->getTeacher()) {
            throw new UnprocessableEntityHttpException('Role duplication');
        }

    }
}
