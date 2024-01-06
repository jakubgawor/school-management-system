<?php

namespace App\Dto;

use App\Validator\ResetPasswordEmail;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class EmailDto
{
    #[ResetPasswordEmail]
    #[NotBlank]
    #[Email]
    public ?string $email = null;
}