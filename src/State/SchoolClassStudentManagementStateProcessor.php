<?php

namespace App\State;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\SchoolClassStudentManagementApi;
use App\Entity\Student;
use App\Service\SchoolClassService;
use App\Service\SchoolClassStudentManagementService;
use Symfonycasts\MicroMapper\MicroMapperInterface;

class SchoolClassStudentManagementStateProcessor implements ProcessorInterface
{
    public function __construct(
        private MicroMapperInterface                $microMapper,
        private SchoolClassService                  $schoolClassService,
        private SchoolClassStudentManagementService $schoolClassStudentManagementService,

    )
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if ($operation instanceof Post) {
            assert($data instanceof SchoolClassStudentManagementApi);

            $student = $this->microMapper->map($data->student, Student::class);
            $schoolClass = $this->schoolClassService->getSchoolClassByName($data->name);

            $this->schoolClassService->addStudentToSchoolClass($schoolClass, $student);
        }

        if ($operation instanceof Delete) {
            assert($data instanceof Student);

            $student = $data;
            $schoolClass = $this->schoolClassService->getSchoolClassByName($uriVariables['schoolClassName']);

            $this->schoolClassStudentManagementService->validateStudentClass($student, $schoolClass);

            $this->schoolClassService->removeStudentFromClass($schoolClass, $student);
        }

        return $data;
    }
}
