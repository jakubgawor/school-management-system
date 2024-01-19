<?php

namespace App\Mapper\Teacher;

use App\Dto\TeacherNameDto;
use App\Entity\Teacher;
use App\Repository\TeacherRepository;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;

#[AsMapper(from: TeacherNameDto::class, to: Teacher::class)]
class TeacherNameDtoToStudentEntityMapper implements MapperInterface
{
    public function __construct(
        private TeacherRepository $teacherRepository,
    )
    {
    }

    public function load(object $from, string $toClass, array $context): object
    {
        $dto = $from;
        assert($dto instanceof TeacherNameDto);

        $teacherEntity = $dto->getId() ? $this->teacherRepository->find($dto->getId()) : new Teacher();

        if (!$teacherEntity) {
            throw new \Exception('Teacher not found.');
        }

        return $teacherEntity;
    }

    public function populate(object $from, object $to, array $context): object
    {
        $dto = $from;
        $entity = $to;
        assert($dto instanceof TeacherNameDto);
        assert($entity instanceof Teacher);

        $entity->setFirstName($dto->firstName);
        $entity->setLastName($dto->lastName);

        return $entity;
    }
}