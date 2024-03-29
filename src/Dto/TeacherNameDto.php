<?php

namespace App\Dto;

use ApiPlatform\Metadata\ApiProperty;

class TeacherNameDto
{
    #[ApiProperty(readable: false, writable: false, identifier: true)]
    private ?int $id = null;

    public ?string $firstName = null;
    public ?string $lastName = null;

    #[ApiProperty(readable: false, writable: false)]
    public array $subject = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

}