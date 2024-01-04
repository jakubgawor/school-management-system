<?php

namespace App\Service;

use App\Repository\UserVerificationTokenRepository;
use Doctrine\ORM\EntityManagerInterface;

class VerificationUserMailService
{
    public function __construct(
        private EntityManagerInterface          $entityManager,
        private UserVerificationTokenRepository $userVerificationTokenRepository,
        private TokenService                    $tokenService,
    )
    {
    }

    public function verifyUser(string $token): void
    {
        $verificationToken = $this->userVerificationTokenRepository->findOneBy(['token' => $token]);

        $user = $verificationToken->getUser();
        $user->setIsVerified(true);
        $user->setRoles(['ROLE_USER_EMAIL_VERIFIED']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->tokenService->removeToken($token);
    }
}