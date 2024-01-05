<?php

namespace App\Mapper;

use App\ApiResource\UserApi;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;

#[AsMapper(from: UserApi::class, to: User::class)]
class UserApiToEntityMapper implements MapperInterface
{
    public function __construct(
        private UserRepository              $userRepository,
        private UserPasswordHasherInterface $userPasswordHasher,
    )
    {
    }

    public function load(object $from, string $toClass, array $context): object
    {
        $dto = $from;
        assert($dto instanceof UserApi);

        $userEntity = $dto->getId() ? $this->userRepository->find($dto->getId()) : new User();

        if (!$userEntity) {
            throw new \Exception('User not found.');
        }

        return $userEntity;
    }

    public function populate(object $from, object $to, array $context): object
    {
        $dto = $from;
        assert($dto instanceof UserApi);

        $entity = $to;
        assert($entity instanceof User);


        $entity->setEmail($dto->getEmail());
        if ($dto->getPassword()) {
            $entity->setPassword($this->userPasswordHasher->hashPassword($entity, $dto->getPassword()));
        }

        return $entity;
    }
}