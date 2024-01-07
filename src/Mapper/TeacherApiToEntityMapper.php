<?php

namespace App\Mapper;

use App\ApiResource\TeacherApi;
use App\Entity\Teacher;
use App\Entity\User;
use App\Repository\TeacherRepository;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: TeacherApi::class, to: Teacher::class)]
class TeacherApiToEntityMapper implements MapperInterface
{
    public function __construct(
        private TeacherRepository    $teacherRepository,
        private MicroMapperInterface $microMapper,
    )
    {
    }

    public function load(object $from, string $toClass, array $context): object
    {
        $dto = $from;
        assert($dto instanceof TeacherApi);

        $teacherEntity = $dto->getId() ? $this->teacherRepository->find($dto->getId()) : new Teacher();

        if (!$teacherEntity) {
            throw new \Exception('User not found.');
        }

        return $teacherEntity;
    }

    public function populate(object $from, object $to, array $context): object
    {
        $dto = $from;
        $entity = $to;
        assert($dto instanceof TeacherApi);
        assert($entity instanceof Teacher);

        $entity->setFirstName($dto->getFirstName());
        $entity->setLastName($dto->getLastName());
        $entity->setUser($this->microMapper->map($dto->getUser(), User::class, [
            MicroMapperInterface::MAX_DEPTH => 0,
        ]));
        $entity->getUser()->setRoles(['ROLE_TEACHER']);

        return $entity;
    }
}