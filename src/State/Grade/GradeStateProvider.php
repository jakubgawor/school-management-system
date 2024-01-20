<?php

namespace App\State\Grade;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\StudentGradeDto;
use App\Repository\GradeRepository;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfonycasts\MicroMapper\MicroMapperInterface;

class GradeStateProvider implements ProviderInterface
{
    public function __construct(
        private GradeRepository      $gradeRepository,
        private MicroMapperInterface $microMapper,
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $grades = $this->gradeRepository->findGradesByStudentAndSubjectName($uriVariables['studentId'], $uriVariables['subjectName']);

        if(!$grades) {
            throw new ResourceNotFoundException('Not found');
        }

        $dtos = [];
        foreach ($grades as $grade) {
            $dtos[] = $this->microMapper->map($grade, StudentGradeDto::class);
        }

        return $dtos;
    }
}
