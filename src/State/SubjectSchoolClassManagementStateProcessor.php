<?php

namespace App\State;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\SubjectSchoolClassDto;
use App\Entity\Subject;
use App\Service\SchoolClassService;
use App\Service\SubjectService;

class SubjectSchoolClassManagementStateProcessor implements ProcessorInterface
{
    public function __construct(
        private SubjectService     $subjectService,
        private SchoolClassService $schoolClassService,
    )
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if ($operation instanceof Delete) {
            assert($data instanceof Subject);
            $subject = $data;
            $schoolClass = $this->schoolClassService->getSchoolClassByName($uriVariables['schoolClassName']);

            $this->subjectService->removeSchoolClassFromSubject($subject, $schoolClass);

            return $subject;
        }

        assert($data instanceof SubjectSchoolClassDto);

        $subject = $this->subjectService->getSubjectById($data->subjectId);
        $schoolClass = $this->schoolClassService->getSchoolClassByName($data->schoolClassName);

        $this->subjectService->teachedSameNameSubject($schoolClass, $subject);

        $this->subjectService->addSchoolClassToSubject($subject, $schoolClass);

        return $data;

    }
}
