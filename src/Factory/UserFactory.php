<?php

namespace App\Factory;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<User>
 *
 * @method        User|Proxy create(array|callable $attributes = [])
 * @method static User|Proxy createOne(array $attributes = [])
 * @method static User|Proxy find(object|array|mixed $criteria)
 * @method static User|Proxy findOrCreate(array $attributes)
 * @method static User|Proxy first(string $sortedField = 'id')
 * @method static User|Proxy last(string $sortedField = 'id')
 * @method static User|Proxy random(array $attributes = [])
 * @method static User|Proxy randomOrCreate(array $attributes = [])
 * @method static UserRepository|RepositoryProxy repository()
 * @method static User[]|Proxy[] all()
 * @method static User[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static User[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static User[]|Proxy[] findBy(array $attributes)
 * @method static User[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static User[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class UserFactory extends ModelFactory
{
    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher,
    )
    {
        parent::__construct();
    }

    public function withRoles(array $roles): self
    {
        return $this->addState(['roles' => $roles]);
    }

    public function asAdmin(): self
    {
        return $this->withRoles(['ROLE_ADMIN']);
    }

    public function asTeacher(): self
    {
        return $this->withRoles(['ROLE_TEACHER']);
    }

    public function asStudent(): self
    {
        return $this->withRoles(['ROLE_STUDENT']);
    }

    protected function getDefaults(): array
    {
        return [
            'email' => self::faker()->email(),
            'password' => 'password',
            'roles' => ['ROLE_USER_EMAIL_VERIFIED'],
            'isVerified' => true,
        ];
    }

    protected function initialize(): self
    {
        return $this
            ->afterInstantiate(function (User $user): void {
                $user->setPassword($this->userPasswordHasher->hashPassword($user, $user->getPassword()));
             });
    }

    protected static function getClass(): string
    {
        return User::class;
    }
}
