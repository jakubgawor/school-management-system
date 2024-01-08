<?php

namespace App\Validator;

use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class OverrideUserRoleValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint, array $context = []): void
    {
        if ($value->getUser()->getId() !== $context['previous_data']->getUser()->getId()) {
            throw new ConflictHttpException('You can not override user to user with this role');
        }

    }
}
