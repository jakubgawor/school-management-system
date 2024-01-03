<?php

namespace App\Service;

use App\Repository\UserVerificationTokenRepository;
use Doctrine\ORM\EntityManagerInterface;

class VerificationUserMailService
{
    public function __construct(
        private EntityManagerInterface          $entityManager,
        private UserVerificationTokenRepository $tokenRepository,
    )
    {
    }

    public function verifyUser(string $token): void
    {
        $verificationToken = $this->tokenRepository->findOneBy(['token' => $token]);

        $user = $verificationToken->getUser();
        $user->setIsVerified(true);
        $user->setRoles(['ROLE_USER_EMAIL_VERIFIED']);

        $verificationToken->setIsUsed(true);

        $this->entityManager->persist($user);
        $this->entityManager->persist($verificationToken);

        $this->entityManager->flush();
    }
}