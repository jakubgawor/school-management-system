<?php

namespace App\State\SchoolClass;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Service\StudentService;

class SchoolClassStudentManagementStateProvider implements ProviderInterface
{
    public function __construct(
        private StudentService $studentService,
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        return $this->studentService->getStudentById($uriVariables['studentId']);
    }
}
