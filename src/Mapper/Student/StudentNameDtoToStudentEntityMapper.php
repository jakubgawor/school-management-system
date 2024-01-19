<?php

namespace App\Mapper\Student;

use App\Dto\StudentNameDto;
use App\Entity\Student;
use App\Repository\StudentRepository;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;

#[AsMapper(from: StudentNameDto::class, to: Student::class)]
class StudentNameDtoToStudentEntityMapper implements MapperInterface
{
    public function __construct(
        private StudentRepository $studentRepository,
    )
    {
    }

    public function load(object $from, string $toClass, array $context): object
    {
        $dto = $from;
        assert($dto instanceof StudentNameDto);

        $studentEntity = $dto->getId() ? $this->studentRepository->find($dto->getId()) : new Student();

        if (!$studentEntity) {
            throw new \Exception('Student not found.');
        }

        return $studentEntity;
    }

    public function populate(object $from, object $to, array $context): object
    {
        $dto = $from;
        $entity = $to;
        assert($dto instanceof StudentNameDto);
        assert($entity instanceof Student);

        $entity->setFirstName($dto->firstName);
        $entity->setLastName($dto->lastName);

        return $entity;
    }
}