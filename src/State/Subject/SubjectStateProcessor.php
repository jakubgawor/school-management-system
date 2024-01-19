<?php

namespace App\State\Subject;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\SubjectApi;
use App\Entity\Teacher;
use App\Service\SubjectService;
use App\State\EntityClassDtoStateProcessor;
use Symfonycasts\MicroMapper\MicroMapperInterface;

class SubjectStateProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityClassDtoStateProcessor $innerProcessor,
        private SubjectService               $subjectService,
        private MicroMapperInterface         $microMapper,
    )
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        assert($data instanceof SubjectApi);

        $teacher = $this->microMapper->map($data->getTeacher(), Teacher::class);

        $this->subjectService->validateSameNameSubject($data->getName(), $teacher);

        return $this->innerProcessor->process($data, $operation, $uriVariables, $context);
    }
}
