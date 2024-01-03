<?php

namespace App\ApiResource;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Entity\User;
use App\State\EntityClassDtoStateProcessor;
use App\State\EntityToDtoStateProvider;
use App\Validator\UniqueEmail;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ApiResource(
    shortName: 'User',
    operations: [
        new Get(),
        new GetCollection(),
        new Post(
            security: 'is_granted("PUBLIC_ACCESS")',
            validationContext: ['groups' => ['Default', 'postValidation']],
        ),
        new Patch(
            security: 'is_granted("PATCH", object)',
        ),
        new Delete(
            security: 'is_granted("DELETE", object)',
        ),
    ],
    paginationItemsPerPage: 10,
    security: 'is_granted("ROLE_USER")',
    provider: EntityToDtoStateProvider::class,
    processor: EntityClassDtoStateProcessor::class,
    stateOptions: new Options(User::class),
)]
class UserApi
{
    #[ApiProperty(readable: false, writable: false, identifier: true)]
    public ?Uuid $id = null;

    #[NotBlank]
    #[Email]
    #[UniqueEmail]
    public ?string $email = null;

    #[NotBlank(groups: ['postValidation'])]
    #[ApiProperty(readable: false)]
    public ?string $password = null;
}