<?php

namespace App\Mapper;

use App\ApiResource\StudentApi;
use App\Entity\Student;
use App\Entity\User;
use App\Repository\StudentRepository;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: StudentApi::class, to: Student::class)]
class StudentApiToEntityMapper implements MapperInterface
{
    public function __construct(
        private StudentRepository    $studentRepository,
        private MicroMapperInterface $microMapper,
    )
    {
    }

    public function load(object $from, string $toClass, array $context): object
    {
        $dto = $from;
        assert($dto instanceof StudentApi);

        $studentEntity = $dto->getId() ? $this->studentRepository->find($dto->getId()) : new Student();

        if (!$studentEntity) {
            throw new \Exception('User not found.');
        }

        return $studentEntity;
    }

    public function populate(object $from, object $to, array $context): object
    {
        $dto = $from;
        $entity = $to;
        assert($dto instanceof StudentApi);
        assert($entity instanceof Student);

        $entity->setFirstName($dto->getFirstName());
        $entity->setLastName($dto->getLastName());
        $entity->setUser($this->microMapper->map($dto->getUser(), User::class, [
            MicroMapperInterface::MAX_DEPTH => 0,
        ]));

        return $entity;
    }
}