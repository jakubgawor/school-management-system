<?php

namespace App\Dto;

class AddGradeDto
{
    /** Subject name */
    public ?string $subject = null;

    public ?string $grade = null;

    public ?int $weight = null;

}