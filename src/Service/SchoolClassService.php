<?php

namespace App\Service;

use App\Entity\SchoolClass;
use App\Entity\Student;
use App\Repository\SchoolClassRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SchoolClassService
{
    public function __construct(
        private SchoolClassRepository  $schoolClassRepository,
        private EntityManagerInterface $entityManager,
    )
    {
    }

    public function removeStudentsFromClass(SchoolClass $class): void
    {
        foreach ($class->getStudents() as $student) {
            $class->removeStudent($student);
        }
    }

    public function getSchoolClassByName(string $name): SchoolClass
    {
        $schoolClass = $this->schoolClassRepository->findOneBy(['name' => $name]);

        if (!$schoolClass) {
            throw new NotFoundHttpException('Resource not found');
        }

        return $schoolClass;
    }

    public function addStudentToSchoolClass(SchoolClass $schoolClass, Student $student): void
    {
        $schoolClass->addStudent($student);
        $this->entityManager->persist($schoolClass);
        $this->entityManager->flush();
    }


    public function removeStudentFromClass(SchoolClass $schoolClass, Student $student): void
    {
        $schoolClass->removeStudent($student);
        $this->entityManager->persist($schoolClass);
        $this->entityManager->flush();
    }
}