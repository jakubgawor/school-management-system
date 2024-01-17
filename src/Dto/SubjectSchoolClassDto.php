<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints\NotBlank;

class SubjectSchoolClassDto
{
    #[NotBlank]
    public ?int $subjectId = null;

    #[NotBlank]
    public ?string $schoolClassName = null;
}