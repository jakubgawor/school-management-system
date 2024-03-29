<?php

namespace App\Service;

use App\Entity\SchoolClass;
use App\Entity\Student;
use App\Entity\Subject;
use App\Entity\Teacher;
use App\Repository\SubjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class SubjectService
{
    public function __construct(
        private SubjectRepository      $subjectRepository,
        private EntityManagerInterface $entityManager,
    )
    {
    }

    public function validateSameNameSubject(string $name, Teacher $teacher): void
    {
        $subjects = $this->subjectRepository->findByName($name);

        if ($subjects) {
            foreach ($subjects as $subject) {
                if ($subject->getTeacher()->getId() === $teacher->getId()) {
                    throw new UnprocessableEntityHttpException('This teacher already teaches a subject by that name!');
                }
            }
        }

    }

    public function taughtSameNameSubject(SchoolClass $schoolClass, Subject $subject): void
    {
        foreach($schoolClass->getSubjects()->getValues() as $value) {
            if($value->getName() === $subject->getName()) {
                throw new UnprocessableEntityHttpException('You cannot add more than one of the same subject to a class!');
            }

        }

    }

    public function getSubjectById(int $id): Subject
    {
        $subject = $this->subjectRepository->findOneById($id);

        if (!$subject) {
            throw new NotFoundHttpException('Resource not found');
        }

        return $subject;
    }

    public function addSchoolClassToSubject(Subject $subject, SchoolClass $schoolClass): void
    {
        $subject->addSchoolClass($schoolClass);
        $this->entityManager->persist($subject);
        $this->entityManager->flush();
    }

    public function removeSchoolClassFromSubject(Subject $subject, SchoolClass $schoolClass): void
    {
        $subject->removeSchoolClass($schoolClass);
        $this->entityManager->persist($subject);
        $this->entityManager->flush();
    }

    public function findMatchingSubjectByNameForStudent(string $subjectName, Student $student): Subject
    {
        $subjects = $this->subjectRepository->findByName($subjectName);

        $subjectIdsWithProvidedName = array_map(function (Subject $subject) {
            return $subject->getId();
        }, $subjects);


        $studentSubjectId = null;
        foreach ($student->getSchoolClass()->getSubjects()->getValues() as $studentSubjects) {
            if (in_array($studentSubjects->getId(), $subjectIdsWithProvidedName)) {
                $studentSubjectId = $studentSubjects->getId();
            }
        }

        if(!$studentSubjectId) {
            throw new UnprocessableEntityHttpException('This student is not taught this subject');
        }

        return $this->subjectRepository->findOneById($studentSubjectId);
    }

}