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
use App\Entity\Subject;
use App\State\EntityClassDtoStateProcessor;
use App\State\EntityToDtoStateProvider;
use App\State\Subject\SubjectStateProcessor;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ApiResource(
    shortName: 'Subject',
    operations: [
        new Get(),
        new GetCollection(),
        new Post(
            security: 'is_granted("ROLE_ADMIN")',
            processor: SubjectStateProcessor::class,
        ),
        new Delete(
            security: 'is_granted("ROLE_TEACHER")',
        ),
        new Patch(
            security: 'is_granted("ROLE_ADMIN")',
            processor: SubjectStateProcessor::class,
        ),
    ],
    security: 'is_granted("ROLE_STUDENT")',
    provider: EntityToDtoStateProvider::class,
    processor: EntityClassDtoStateProcessor::class,
    stateOptions: new Options(Subject::class),
)]
class SubjectApi
{
    #[ApiProperty(readable: false, writable: false, identifier: true)]
    private ?int $id = null;

    #[NotBlank]
    private ?string $name = null;

    #[ApiProperty(writable: false)]
    private array $schoolClasses = [];

    #[NotBlank]
    private ?TeacherApi $teacher = null;

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

    public function getSchoolClasses(): array
    {
        return $this->schoolClasses;
    }

    public function setSchoolClasses(array $schoolClasses): void
    {
        $this->schoolClasses = $schoolClasses;
    }

    public function getTeacher(): ?TeacherApi
    {
        return $this->teacher;
    }

    public function setTeacher(?TeacherApi $teacher): void
    {
        $this->teacher = $teacher;
    }


}