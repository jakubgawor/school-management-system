<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Dto\EmailDto;
use App\Dto\ResetPasswordDto;
use App\State\EntityToDtoStateProvider;
use App\State\ResetPassword\ResetPasswordStateProcessor;

#[ApiResource(
    shortName: 'Reset Password',
    operations: [
        new Post(
            uriTemplate: '/account/reset-password',
            input: EmailDto::class
        ),
        new Post(
            uriTemplate: '/account/reset-password/confirm.{_format}',
            input: ResetPasswordDto::class,
            output: false,
        )
    ],
    security: 'is_granted("PUBLIC_ACCESS")',
    provider: EntityToDtoStateProvider::class,
    processor: ResetPasswordStateProcessor::class
)]
class ResetPasswordApi
{

}