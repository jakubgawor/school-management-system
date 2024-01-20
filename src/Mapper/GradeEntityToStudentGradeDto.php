<?php

namespace App\Mapper;

use App\Dto\StudentGradeDto;
use App\Entity\Grade;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;

#[AsMapper(from: Grade::class, to: StudentGradeDto::class)]
class GradeEntityToStudentGradeDto implements MapperInterface
{

    public function load(object $from, string $toClass, array $context): object
    {
        $entity = $from;

        $dto =  new StudentGradeDto();
        $dto->id = $entity->getId();

        return $dto;
    }

    public function populate(object $from, object $to, array $context): object
    {
        $entity = $from;
        $dto = $to;
        assert($entity instanceof Grade);
        assert($dto instanceof StudentGradeDto);

        $dto->grade = $entity->getGrade();
        $dto->weight = $entity->getWeight();

        return $dto;
    }
}