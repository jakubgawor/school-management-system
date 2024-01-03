<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\State\EntityToDtoStateProvider;
use App\State\UserVerificationTokenStateProcessor;
use App\Validator\VerificationToken;

#[ApiResource(
    uriTemplate: '/account/confirm.{_format}',
    shortName: 'Email Verification',
    operations: [
        new Post()
    ],
    provider: EntityToDtoStateProvider::class,
    processor: UserVerificationTokenStateProcessor::class
)]
class UserVerificationTokenApi
{
    #[VerificationToken]
    private ?string $token = null;

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): void
    {
        $this->token = $token;
    }

}