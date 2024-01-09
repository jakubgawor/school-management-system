<?php

namespace App\ApiResource;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Entity\SchoolClass;
use App\State\EntityToDtoStateProvider;
use App\State\SchoolClassStateProcessor;
use App\Validator\UniqueSchoolClassName;

// todo search filter
#[ApiResource(
    shortName: 'Class',
    operations: [
        new Get(),
        new GetCollection(),
        new Post(
            security: 'is_granted("ROLE_ADMIN")'
        ),
        new Delete(
            security: 'is_granted("ROLE_ADMIN")'
        ),
    ],
    security: 'is_granted("ROLE_TEACHER")',
    provider: EntityToDtoStateProvider::class,
    processor: SchoolClassStateProcessor::class,
    stateOptions: new Options(SchoolClass::class),
)]
class SchoolClassApi
{
    #[ApiProperty(readable: false, writable: false, identifier: true)]
    private ?int $id = null;

    #[UniqueSchoolClassName]
    private ?string $name = null;

    /**
     * @var array<int, StudentApi>
     */
    #[ApiProperty(writable: false)]
    private array $students = [];


    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getStudents(): ?array
    {
        return $this->students;
    }

    public function setStudents(?array $students): void
    {
        $this->students = $students;
    }


}