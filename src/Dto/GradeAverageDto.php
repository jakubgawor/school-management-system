<?php

namespace App\Dto;

use ApiPlatform\Metadata\ApiProperty;

class GradeAverageDto
{
    public ?float $average = null;

    #[ApiProperty(readable: false, writable: false)]
    public ?int $studentId = null;

    #[ApiProperty(readable: false, writable: false)]
    public ?string $subjectName = null;
}