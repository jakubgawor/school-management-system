<?php

namespace App\State;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\SchoolClass;
use App\Service\SchoolClassService;
use Symfonycasts\MicroMapper\MicroMapperInterface;

class SchoolClassStateProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityClassDtoStateProcessor $innerProcessor,
        private MicroMapperInterface         $microMapper,
        private SchoolClassService           $schoolClassService,
    )
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if ($operation instanceof Delete) {
            $this->schoolClassService->removeStudentsFromClass($this->microMapper->map(
                $data,
                SchoolClass::class
            ));
        }

        return $this->innerProcessor->process($data, $operation, $uriVariables, $context);
    }
}
