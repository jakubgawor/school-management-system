<?php

namespace App\Factory;

use App\Entity\UserVerificationToken;
use App\Repository\UserVerificationTokenRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<UserVerificationToken>
 *
 * @method        UserVerificationToken|Proxy create(array|callable $attributes = [])
 * @method static UserVerificationToken|Proxy createOne(array $attributes = [])
 * @method static UserVerificationToken|Proxy find(object|array|mixed $criteria)
 * @method static UserVerificationToken|Proxy findOrCreate(array $attributes)
 * @method static UserVerificationToken|Proxy first(string $sortedField = 'id')
 * @method static UserVerificationToken|Proxy last(string $sortedField = 'id')
 * @method static UserVerificationToken|Proxy random(array $attributes = [])
 * @method static UserVerificationToken|Proxy randomOrCreate(array $attributes = [])
 * @method static UserVerificationTokenRepository|RepositoryProxy repository()
 * @method static UserVerificationToken[]|Proxy[] all()
 * @method static UserVerificationToken[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static UserVerificationToken[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static UserVerificationToken[]|Proxy[] findBy(array $attributes)
 * @method static UserVerificationToken[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static UserVerificationToken[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class UserVerificationTokenFactory extends ModelFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function getDefaults(): array
    {
        return [
            'token' => bin2hex(random_bytes(32)),
            'user' => UserFactory::createOne([
                'roles' => [],
                'isVerified' => false,
            ]),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(UserVerificationToken $userVerificationToken): void {})
        ;
    }

    protected static function getClass(): string
    {
        return UserVerificationToken::class;
    }
}
