<?php

namespace App\Service;

use App\ApiResource\UserApi;
use App\Entity\User;
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

        if(!$userEntity) {
            throw new \LogicException('User not found');
        }

        $existingToken = $this->userVerificationTokenRepository->findOneBy(['user' => $userEntity]);
        if($existingToken) {
            $this->removeToken($existingToken->getToken());
        }

        $token = bin2hex(random_bytes(32));

        $this->saveToken($token, $userEntity);

        return $token;
    }

    public function saveToken(string $token, User $userEntity)
    {
        $verificationToken = new UserVerificationToken();
        $verificationToken->setToken($token);
        $verificationToken->setUser($userEntity);
        $userEntity->setUserVerificationToken($verificationToken);

        $this->entityManager->persist($verificationToken);
        $this->entityManager->persist($userEntity);
        $this->entityManager->flush();
    }

    public function removeToken(string $token): void
    {
        $verificationToken = $this->userVerificationTokenRepository->findOneBy(['token' => $token]);

        $this->entityManager->remove($verificationToken);
        $this->entityManager->flush();
    }

}