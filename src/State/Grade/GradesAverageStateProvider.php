<?php

namespace App\State\Grade;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\GradeAverageDto;
use App\Service\GradeService;

class GradesAverageStateProvider implements ProviderInterface
{
    public function __construct(
        private GradeStateProvider $stateProvider,
        private GradeService       $gradeService,
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $grades = $this->stateProvider->provide($operation, $uriVariables, $context);
        $average = $this->gradeService->averageGrade($grades);

        $averageDto = new GradeAverageDto();
        $averageDto->average = $average;

        return $averageDto;
    }
}
