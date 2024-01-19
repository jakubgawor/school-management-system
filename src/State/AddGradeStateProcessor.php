<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Enum\GradeEnum;
use App\Service\GradeService;
use App\Service\StudentService;
use App\Service\SubjectService;
use Symfony\Bundle\SecurityBundle\Security;

class AddGradeStateProcessor implements ProcessorInterface
{
    public function __construct(
        private Security               $security,
        private StudentService         $studentService,
        private SubjectService         $subjectService,
        private GradeService           $gradeService,
    )
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $student = $this->studentService->getStudentById($uriVariables['studentId']);
        $studentSubject = $this->subjectService->findMatchingSubjectByNameForStudent($data->subject, $student);

        $this->gradeService->verifyTeacherForSubject($this->security->getUser(), $studentSubject);

        $this->gradeService->addGrade(
            GradeEnum::fromString($data->grade)->value,
            $data->weight,
            $student,
            $studentSubject,
            $studentSubject->getTeacher(),
        );

    }
}
