<?php

namespace App\State\Grade;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\StudentGradeDto;
use App\Service\GradeService;
use Symfonycasts\MicroMapper\MicroMapperInterface;

class GradeStateProvider implements ProviderInterface
{
    public function __construct(
        private GradeService         $gradeService,
        private MicroMapperInterface $microMapper,
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation instanceof Delete) {
            return $this->gradeService->findGradeById($uriVariables['gradeId']);
        }

        $grades = $this->gradeService->findGradesByStudentIdAndSubjectName($uriVariables['studentId'], $uriVariables['subjectName']);

        $dtos = [];
        foreach ($grades as $grade) {
            $dtos[] = $this->microMapper->map($grade, StudentGradeDto::class);
        }

        return $dtos;
    }
}
