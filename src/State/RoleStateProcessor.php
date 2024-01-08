<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use App\Validator\OverrideUserRole;
use App\Validator\OverrideUserRoleValidator;
use App\Validator\UserRoleExistence;
use App\Validator\UserRoleExistenceValidator;
use Symfonycasts\MicroMapper\MicroMapperInterface;

class RoleStateProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityClassDtoStateProcessor $innerProcessor,
        private MicroMapperInterface         $microMapper,
        private OverrideUserRoleValidator    $overrideUserRoleValidator,
        private UserRoleExistenceValidator   $userRoleExistanceValidator,
    )
    {
    }


    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if ($operation instanceof Patch) {
            $this->overrideUserRoleValidator->validate($data, new OverrideUserRole(), $context);
        }

        if ($operation instanceof Post) {
            $user = $this->microMapper->map($data->getUser(), User::class);
            $this->userRoleExistanceValidator->validate($user, new UserRoleExistence());
        }


        return $this->innerProcessor->process($data, $operation, $uriVariables, $context);
    }
}
