<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Service\SubjectService;

class SubjectSchoolClassManagementStateProvider implements ProviderInterface
{
    public function __construct(
        private SubjectService $subjectService,
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        return $this->subjectService->getSubjectById($uriVariables['subjectId']);
    }
}
