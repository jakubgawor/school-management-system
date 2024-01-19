<?php

namespace App\Mapper\Subject;

use App\ApiResource\SchoolClassApi;
use App\ApiResource\SubjectApi;
use App\ApiResource\TeacherApi;
use App\Entity\SchoolClass;
use App\Entity\Subject;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: Subject::class, to: SubjectApi::class)]
class SubjectEntityToApiMapper implements MapperInterface
{
    public function __construct(
        private MicroMapperInterface $microMapper,
    )
    {
    }

    public function load(object $from, string $toClass, array $context): object
    {
        $entity = $from;
        assert($entity instanceof Subject);

        $dto = new SubjectApi();
        $dto->setId($entity->getId());

        return $dto;
    }

    public function populate(object $from, object $to, array $context): object
    {
        $entity = $from;
        $dto = $to;
        assert($entity instanceof Subject);
        assert($dto instanceof SubjectApi);

        $dto->setName($entity->getName());

        $dto->setTeacher($this->microMapper->map($entity->getTeacher(), TeacherApi::class, [
            MicroMapperInterface::MAX_DEPTH => 1,
        ]));

        $dto->setSchoolClasses(array_map(function (SchoolClass $schoolClass) {
            return $this->microMapper->map($schoolClass, SchoolClassApi::class, [
                MicroMapperInterface::MAX_DEPTH => 1,
            ])->getName();
        }, $entity->getSchoolClasses()->getValues()));


        return $dto;
    }
}