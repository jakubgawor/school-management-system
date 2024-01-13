<?php

namespace App\Service;

use App\Entity\Student;
use App\Repository\StudentRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class StudentService
{
    public function __construct(
        private StudentRepository $studentRepository,
    )
    {
    }

    public function getStudentById(int $studentId): Student
    {
        $student = $this->studentRepository->findOneBy(['id' => $studentId]);

        if (!$student) {
            throw new NotFoundHttpException('Resource not found');
        }

        return $student;
    }
}