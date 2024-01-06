<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ResetPasswordService
{
    public function __construct(
        private UserPasswordHasherInterface     $userPasswordHasher,
        private EntityManagerInterface          $entityManager,
        private TokenService                    $tokenService,
    )
    {
    }

    public function resetPassword(string $token, string $plainPassword): void
    {
        $verificationToken = $this->tokenService->getUserVerificationTokenEntity($token);

        $user = $verificationToken->getUser();

        $user->setPassword($this->userPasswordHasher->hashPassword(
            $user,
            $plainPassword
        ));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->tokenService->removeToken($user->getUserVerificationToken()->getToken());
    }
}