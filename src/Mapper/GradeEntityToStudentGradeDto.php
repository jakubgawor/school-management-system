<?php

namespace App\Mapper;

use App\ApiResource\TeacherApi;
use App\Dto\StudentGradeDto;
use App\Entity\Grade;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: Grade::class, to: StudentGradeDto::class)]
class GradeEntityToStudentGradeDto implements MapperInterface
{
    public function __construct(
        private MicroMapperInterface $microMapper,
    )
    {
    }

    public function load(object $from, string $toClass, array $context): object
    {
        $entity = $from;

        $dto = new StudentGradeDto();
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
        $dto->issuedBy = $this->microMapper->map($entity->getTeacher(), TeacherApi::class);

        return $dto;
    }
}