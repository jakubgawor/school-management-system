<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Type;

class AddGradeDto
{
    /** Subject name */
    #[NotBlank]
    #[NotNull]
    public ?string $subject = null;

    #[NotBlank]
    #[NotNull]
    public ?string $grade = null;

    #[NotBlank]
    #[NotNull]
    #[Range(min: 1, max: 5)]
    #[Type(type: ['integer', 'digit'])]
    public ?int $weight = null;

}