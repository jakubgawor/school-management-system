<?php

namespace App\Dto;

use App\ApiResource\TeacherApi;

class StudentGradeDto
{
    public ?int $id = null;

    public ?string $grade = null;

    public ?int $weight = null;

    public ?TeacherApi $issuedBy = null;
}