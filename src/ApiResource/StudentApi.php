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
use App\Dto\StudentNameDto;
use App\Entity\Student;
use App\State\EntityToDtoStateProvider;
use App\State\RoleStateProcessor;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ApiResource(
    shortName: 'Student',
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
        new Delete(),
        new Patch(input: StudentNameDto::class),
    ],
    security: 'is_granted("ROLE_ADMIN") or is_granted("ROLE_TEACHER")',
    provider: EntityToDtoStateProvider::class,
    processor: RoleStateProcessor::class,
    stateOptions: new Options(Student::class),
)]
class StudentApi
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

    #[ApiProperty(readable: true, writable: false)]
    private ?SchoolClassApi $schoolClass = null;

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

    /**
     * @return SchoolClassApi|null
     */
    public function getSchoolClass(): ?SchoolClassApi
    {
        return $this->schoolClass;
    }

    /**
     * @param SchoolClassApi|null $schoolClass
     */
    public function setSchoolClass(?SchoolClassApi $schoolClass): void
    {
        $this->schoolClass = $schoolClass;
    }

}