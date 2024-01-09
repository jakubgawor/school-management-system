<?php

namespace App\Mapper;

use App\ApiResource\SchoolClassApi;
use App\ApiResource\StudentApi;
use App\Entity\SchoolClass;
use App\Entity\Student;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: SchoolClass::class, to: SchoolClassApi::class)]
class SchoolClassEntityToApiMapper implements MapperInterface
{
    public function __construct(
        private MicroMapperInterface $microMapper,
    )
    {
    }

    public function load(object $from, string $toClass, array $context): object
    {
        $entity = $from;
        assert($entity instanceof SchoolClass);

        $dto = new SchoolClassApi();
        $dto->setId($entity->getId());

        return $dto;
    }

    public function populate(object $from, object $to, array $context): object
    {
        $entity = $from;
        $dto = $to;
        assert($entity instanceof SchoolClass);
        assert($dto instanceof SchoolClassApi);

        $dto->setName($entity->getName());
        $dto->setStudents(array_map(function (Student $student) {
            return $this->microMapper->map($student, StudentApi::class, [
                MicroMapperInterface::MAX_DEPTH => 0,
            ]);
        }, $entity->getStudents()->getValues()));

        return $dto;
    }
}