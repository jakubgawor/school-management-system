<?php

namespace App\State;

use ApiPlatform\Doctrine\Common\State\PersistProcessor;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\UserVerificationTokenApi;
use App\Service\VerificationUserMailService;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class UserVerificationTokenStateProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: PersistProcessor::class)] private ProcessorInterface $processor,
        private VerificationUserMailService                                      $verificationUserMailService,
    )
    {

    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        assert($data instanceof UserVerificationTokenApi);

        $this->verificationUserMailService->verifyUser($data->getToken());

        $this->processor->process($data, $operation, $uriVariables, $context);


        return $data;
    }
}
