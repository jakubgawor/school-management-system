<?php

namespace App\State\Grade;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Service\GradeService;

class DeleteGradeStateProcessor implements ProcessorInterface
{
    public function __construct(
        private GradeService $gradeService,
    )
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $this->gradeService->removeGrade($data);
    }
}
