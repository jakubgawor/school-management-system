<?php

namespace App\Service;

use App\ApiResource\UserApi;
use App\Entity\UserVerificationToken;
use App\Repository\UserRepository;
use App\Repository\UserVerificationTokenRepository;
use Doctrine\ORM\EntityManagerInterface;

class TokenService
{
    public function __construct(
        private EntityManagerInterface          $entityManager,
        private UserRepository                  $userRepository,
        private UserVerificationTokenRepository $userVerificationTokenRepository,
    )
    {
    }

    public function createToken(UserApi $userApi): string
    {
        $userEntity = $this->userRepository->findOneBy(['id' => $userApi->id]);

        $token = bin2hex(random_bytes(32));
        if($this->userVerificationTokenRepository->findOneBy(['token' => $token])) {
            throw new \LogicException('Token is already created');
        }

        $verificationToken = new UserVerificationToken();
        $verificationToken->setToken($token);
        $verificationToken->setUser($userEntity);
        $verificationToken->setExpiresAt(new \DateTimeImmutable('+15 minutes'));

        $this->saveToken($verificationToken);

        return $verificationToken->getToken();
    }

    private function saveToken(UserVerificationToken $verificationToken): void
    {
        $this->entityManager->persist($verificationToken);
        $this->entityManager->flush();
    }
}