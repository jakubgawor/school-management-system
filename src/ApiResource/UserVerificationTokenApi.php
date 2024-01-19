<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\State\EntityToDtoStateProvider;
use App\State\Token\UserVerificationTokenStateProcessor;
use App\Validator\VerificationToken;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ApiResource(
    uriTemplate: '/account/confirm.{_format}',
    shortName: 'Email Verification',
    operations: [
        new Post()
    ],
    security: 'is_granted("ROLE_USER")',
    provider: EntityToDtoStateProvider::class,
    processor: UserVerificationTokenStateProcessor::class,
)]
class UserVerificationTokenApi
{
    #[VerificationToken]
    #[NotBlank]
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