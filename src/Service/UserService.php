<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Uid\Uuid;

class UserService
{
    public function __construct(
        private UserRepository $userRepository,
    )
    {
    }

    public function findUserByEmail(string $email): ?User
    {
        return $this->userRepository->findOneByEmail($email);
    }

    public function findUserByUuid(Uuid $uuid): User
    {
        $userEntity = $this->userRepository->findOneBy(['id' => $uuid]);

        if (!$userEntity) {
            throw new NotFoundHttpException('User not found');
        }

        return $userEntity;
    }
}