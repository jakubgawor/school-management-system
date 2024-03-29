<?php

namespace App\Service;

use App\Entity\Grade;
use App\Entity\Student;
use App\Entity\Subject;
use App\Entity\Teacher;
use App\Entity\User;
use App\Repository\GradeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

class GradeService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private GradeRepository        $gradeRepository,
    )
    {
    }

    public function verifyTeacherForSubject(User|UserInterface $user, Subject $subject): void
    {
        if ($user->getTeacher()->getId() !== $subject->getTeacher()->getId()) {
            throw new UnprocessableEntityHttpException('You are not teaching this subject and you can not add a grade to this student.');
        }
    }

    public function addGrade(string $gradeValue, int $gradeWeight, Student $student, Subject $subject, Teacher $teacher): void
    {
        $grade = new Grade();

        $grade->setGrade($gradeValue);
        $grade->setWeight($gradeWeight);
        $grade->setStudent($student);
        $grade->setSubject($subject);
        $grade->setTeacher($teacher);

        $this->entityManager->persist($grade);
        $this->entityManager->flush();
    }

    public function removeGrade(Grade $grade): void
    {
        $this->entityManager->remove($grade);
        $this->entityManager->flush();
    }

    public function averageGrade(array $grades): float
    {
        $totalWeightedGrade = 0;
        $totalWeight = 0;

        foreach ($grades as $grade) {
            $totalWeightedGrade += $grade->grade * $grade->weight;
            $totalWeight += $grade->weight;
        }

        return round($totalWeightedGrade / $totalWeight, 2);
    }

    public function findGradeById(int $id): Grade
    {
        $grade = $this->gradeRepository->findOneBy(['id' => $id]);

        if (!$grade) {
            throw new ResourceNotFoundException('Not found');
        }

        return $grade;
    }

    public function findGradesByStudentIdAndSubjectName(int $studentId, string $subjectName): array
    {
        $grades = $this->gradeRepository->findGradesByStudentAndSubjectName($studentId, $subjectName);

        if(empty($grades)) {
            throw new ResourceNotFoundException('This student has no grades in this subject');
        }

        return $grades;
    }
}