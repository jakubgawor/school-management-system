<?php

namespace App\Mapper;

use App\ApiResource\TeacherApi;
use App\ApiResource\UserApi;
use App\Entity\Teacher;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: Teacher::class, to: TeacherApi::class)]
class TeacherEntityToApiMapper implements MapperInterface
{
    public function __construct(
        private MicroMapperInterface $microMapper,
    )
    {
    }

    public function load(object $from, string $toClass, array $context): object
    {
        $entity = $from;
        assert($entity instanceof Teacher);

        $dto = new TeacherApi();
        $dto->setId($entity->getId());

        return $dto;
    }

    public function populate(object $from, object $to, array $context): object
    {
        $entity = $from;
        $dto = $to;
        assert($entity instanceof Teacher);
        assert($dto instanceof TeacherApi);

        $dto->setFirstName($entity->getFirstName());
        $dto->setLastName($entity->getLastName());
        $dto->setUser($this->microMapper->map($entity->getUser(), UserApi::class, [
            MicroMapperInterface::MAX_DEPTH => 0,
        ]));

        return $dto;
    }
}