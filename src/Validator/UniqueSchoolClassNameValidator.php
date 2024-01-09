<?php

namespace App\Validator;

use App\Repository\SchoolClassRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueSchoolClassNameValidator extends ConstraintValidator
{
    public function __construct(
        private SchoolClassRepository $schoolClassRepository,
    )
    {
    }

    public function validate($value, Constraint $constraint)
    {
        if ($this->schoolClassRepository->findOneBy(['name' => $value])) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }

    }
}
