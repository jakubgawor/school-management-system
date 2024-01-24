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
    public function __construct(
        private SchoolClassRepository $schoolClassRepository,
    )
    {
        parent::__construct();
    }

    public function withStudents(int $numberOfStudents): self
    {
        return $this->addState(['students' => StudentFactory::createMany($numberOfStudents)]);
    }

    protected function getDefaults(): array
    {
        return [
            'name' => $this->generateUniqueName(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this// ->afterInstantiate(function(SchoolClass $schoolClass): void {})
            ;
    }

    protected static function getClass(): string
    {
        return SchoolClass::class;
    }

    private function generateUniqueName(): string
    {
        for ($attempts = 0, $maxAttempts = 100; $attempts < $maxAttempts; $attempts++) {
            $name = self::faker()->randomDigit() . self::faker()->randomLetter();
            if ($this->isNameUsed($name)) {
                return $name;
            }
        }

        throw new \Exception('Failed to generate a unique name');
    }

    private function isNameUsed(string $name): bool
    {
        if ($this->schoolClassRepository->findBy(['name' => $name])) {
            return false;
        }

        return true;
    }

}
