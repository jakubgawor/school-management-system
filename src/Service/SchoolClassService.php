<?php

namespace App\Service;

use App\Entity\SchoolClass;

class SchoolClassService
{
    public function removeStudentsFromClass(SchoolClass $class): void
    {
        foreach ($class->getStudents() as $student) {
            $class->removeStudent($student);
        }
    }
}