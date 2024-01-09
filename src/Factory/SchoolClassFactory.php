<?php

namespace App\Factory;

use App\Entity\SchoolClass;
use App\Repository\SchoolClassRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<SchoolClass>
 *
 * @method        SchoolClass|Proxy create(array|callable $attributes = [])
 * @method static SchoolClass|Proxy createOne(array $attributes = [])
 * @method static SchoolClass|Proxy find(object|array|mixed $criteria)
 * @method static SchoolClass|Proxy findOrCreate(array $attributes)
 * @method static SchoolClass|Proxy first(string $sortedField = 'id')
 * @method static SchoolClass|Proxy last(string $sortedField = 'id')
 * @method static SchoolClass|Proxy random(array $attributes = [])
 * @method static SchoolClass|Proxy randomOrCreate(array $attributes = [])
 * @method static SchoolClassRepository|RepositoryProxy repository()
 * @method static SchoolClass[]|Proxy[] all()
 * @method static SchoolClass[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static SchoolClass[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static SchoolClass[]|Proxy[] findBy(array $attributes)
 * @method static SchoolClass[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static SchoolClass[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class SchoolClassFactory extends ModelFactory
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
            'name' => self::faker()->randomDigit() . self::faker()->randomLetter(),
            'students' => StudentFactory::createMany(2),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(SchoolClass $schoolClass): void {})
        ;
    }

    protected static function getClass(): string
    {
        return SchoolClass::class;
    }
}
