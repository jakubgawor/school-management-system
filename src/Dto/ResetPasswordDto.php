<?php

namespace App\Dto;

use ApiPlatform\Metadata\ApiProperty;
use App\Validator\VerificationToken;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ResetPasswordDto
{
    #[NotBlank]
    #[Length(64)]
    #[VerificationToken]
    public ?string $token = null;

    #[NotBlank]
    #[ApiProperty(readable: false)]
    public ?string $password = null;
}