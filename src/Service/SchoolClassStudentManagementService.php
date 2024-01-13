<?php

namespace App\Service;

use App\Entity\SchoolClass;
use App\Entity\Student;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class SchoolClassStudentManagementService
{
    public function validateStudentClass(Student $student, SchoolClass $schoolClass): void
    {
        if ($student->getSchoolClass() !== $schoolClass) {
            throw new UnprocessableEntityHttpException('The user does not belong to this class');
        }

    }
}