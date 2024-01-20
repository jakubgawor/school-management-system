<?php

namespace App\Factory;

use App\Entity\Grade;
use App\Repository\GradeRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Grade>
 *
 * @method        Grade|Proxy create(array|callable $attributes = [])
 * @method static Grade|Proxy createOne(array $attributes = [])
 * @method static Grade|Proxy find(object|array|mixed $criteria)
 * @method static Grade|Proxy findOrCreate(array $attributes)
 * @method static Grade|Proxy first(string $sortedField = 'id')
 * @method static Grade|Proxy last(string $sortedField = 'id')
 * @method static Grade|Proxy random(array $attributes = [])
 * @method static Grade|Proxy randomOrCreate(array $attributes = [])
 * @method static GradeRepository|RepositoryProxy repository()
 * @method static Grade[]|Proxy[] all()
 * @method static Grade[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Grade[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static Grade[]|Proxy[] findBy(array $attributes)
 * @method static Grade[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Grade[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class GradeFactory extends ModelFactory
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
            'grade' => '5.00',
            'student' => StudentFactory::new(),
            'subject' => SubjectFactory::new(),
            'teacher' => TeacherFactory::new(),
            'weight' => '2',
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Grade $grade): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Grade::class;
    }
}
