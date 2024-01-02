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
use App\State\EntityToDtoStateProvider;
use Symfony\Component\Uid\Uuid;

#[ApiResource(
    shortName: 'User',
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
        new Patch(),
        new Delete(),
    ],
    paginationItemsPerPage: 10,
    provider: EntityToDtoStateProvider::class,
    stateOptions: new Options(User::class),
)]
class UserApi
{
    #[ApiProperty(readable: false, writable: false, identifier: true)]
    public ?Uuid $id = null;

    public ?string $email = null;
}