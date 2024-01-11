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
use App\Dto\TeacherNameDto;
use App\Entity\Teacher;
use App\State\EntityToDtoStateProvider;
use App\State\RoleStateProcessor;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ApiResource(
    shortName: 'Teacher',
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
        new Delete(),
        new Patch(input: TeacherNameDto::class),
    ],
    security: 'is_granted("ROLE_ADMIN")',
    provider: EntityToDtoStateProvider::class,
    processor: RoleStateProcessor::class,
    stateOptions: new Options(Teacher::class),
)]
class TeacherApi
{
    #[ApiProperty(readable: false, writable: false, identifier: true)]
    private ?int $id = null;

    #[NotBlank]
    private ?string $firstName = null;

    #[NotBlank]
    private ?string $lastName = null;

    #[NotBlank]
    #[ApiProperty(readable: false)]
    private ?UserApi $user = null;

    /**
     * @return UserApi|null
     */
    public function getUser(): ?UserApi
    {
        return $this->user;
    }

    /**
     * @param UserApi|null $user
     */
    public function setUser(?UserApi $user): void
    {
        $this->user = $user;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

}