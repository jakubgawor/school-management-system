<?php

namespace App\Mapper;

use App\ApiResource\SchoolClassApi;
use App\Entity\SchoolClass;
use App\Repository\SchoolClassRepository;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;

#[AsMapper(from: SchoolClassApi::class, to: SchoolClass::class)]
class SchoolClassApiToEntityMapper implements MapperInterface
{
    public function __construct(
        private SchoolClassRepository $schoolClassRepository,
    )
    {
    }

    public function load(object $from, string $toClass, array $context): object
    {
        $dto = $from;
        assert($dto instanceof SchoolClassApi);

        $schoolClassEntity = $dto->getId() ? $this->schoolClassRepository->find($dto->getId()) : new SchoolClass();

        if (!$schoolClassEntity) {
            throw new \Exception('User not found.');
        }

        return $schoolClassEntity;
    }

    public function populate(object $from, object $to, array $context): object
    {
        $dto = $from;
        $entity = $to;
        assert($dto instanceof SchoolClassApi);
        assert($entity instanceof SchoolClass);

        $entity->setName($dto->getName());

        return $entity;
    }
}