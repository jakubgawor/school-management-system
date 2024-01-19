<?php

namespace App\Mapper\Subject;

use App\ApiResource\SubjectApi;
use App\Entity\Subject;
use App\Entity\Teacher;
use App\Repository\SchoolClassRepository;
use App\Repository\SubjectRepository;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: SubjectApi::class, to: Subject::class)]
class SubjectApiToEntityMapper implements MapperInterface
{
    public function __construct(
        private SubjectRepository         $subjectRepository,
        private MicroMapperInterface      $microMapper,
        private SchoolClassRepository     $schoolClassRepository,

    )
    {
    }

    public function load(object $from, string $toClass, array $context): object
    {
        $dto = $from;
        assert($dto instanceof SubjectApi);

        $subjectEntity = $dto->getId() ? $this->subjectRepository->find($dto->getId()) : new Subject();

        if (!$subjectEntity) {
            throw new \Exception('Subject not found.');
        }

        return $subjectEntity;
    }

    public function populate(object $from, object $to, array $context): object
    {
        $dto = $from;
        $entity = $to;
        assert($dto instanceof SubjectApi);
        assert($entity instanceof Subject);

        $entity->setName($dto->getName());

        foreach ($dto->getSchoolClasses() as $schoolClassName) {
            $schoolClass = $this->schoolClassRepository->findOneBy(['name' => $schoolClassName]);
            $entity->addSchoolClass($schoolClass);
        }

        $entity->setTeacher($this->microMapper->map($dto->getTeacher(), Teacher::class, [
            MicroMapperInterface::MAX_DEPTH => 0,
        ]));

        return $entity;
    }
}