<?php

namespace App\Mapper;

use App\ApiResource\SchoolClassApi;
use App\ApiResource\StudentApi;
use App\ApiResource\UserApi;
use App\Entity\Student;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: Student::class, to: StudentApi::class)]
class StudentEntityToApiMapper implements MapperInterface
{
    public function __construct(
        private MicroMapperInterface $microMapper,
    )
    {
    }

    public function load(object $from, string $toClass, array $context): object
    {
        $entity = $from;
        assert($entity instanceof Student);

        $dto = new StudentApi();
        $dto->setId($entity->getId());

        return $dto;
    }

    public function populate(object $from, object $to, array $context): object
    {
        $entity = $from;
        $dto = $to;
        assert($entity instanceof Student);
        assert($dto instanceof StudentApi);

        $dto->setFirstName($entity->getFirstName());
        $dto->setLastName($entity->getLastName());
        $dto->setUser($this->microMapper->map($entity->getUser(), UserApi::class, [
            MicroMapperInterface::MAX_DEPTH => 0,
        ]));
        if ($entity->getSchoolClass()) {
            $dto->setSchoolClass($this->microMapper->map($entity->getSchoolClass(), SchoolClassApi::class, [
                MicroMapperInterface::MAX_DEPTH => 0,
            ]));
        }

        return $dto;
    }
}