<?php

namespace App\Tests\Functional\SchoolClass;

use App\Factory\SchoolClassFactory;
use App\Factory\StudentFactory;
use App\Factory\UserFactory;
use App\Tests\Functional\Helper\ApiTestCase;
use Zenstruck\Browser\Json;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class SchoolClassTest extends ApiTestCase
{
    use ResetDatabase;
    use Factories;

    /** @test */
    public function retrieving_collection_of_classes()
    {
        SchoolClassFactory::createMany(2);

        $this->browser()
            ->actingAs(UserFactory::new()->asAdmin()->create())
            ->get('/api/classes')
            ->assertJsonMatches('"hydra:totalItems"', 2)
            ->use(function (Json $json) {
                $json->assertMatches('keys("hydra:member"[0])', [
                    '@id',
                    '@type',
                    'name',
                    'students'
                ]);
            });

    }

    /** @test */
    public function retrieve_class_resource()
    {
        $class = SchoolClassFactory::createOne();

        $this->browser()
            ->actingAs(UserFactory::new()->asAdmin()->create())
            ->get('/api/classes/' . $class->getId())
            ->assertStatus(200)
            ->assertJsonMatches('name', $class->getName());

    }

    /** @test */
    public function post_to_create_class_is_saving_to_database()
    {
        $repository = SchoolClassFactory::repository();

        $this->browser()
            ->actingAs(UserFactory::new()->asAdmin()->create())
            ->post('/api/classes', [
                'json' => [
                    'name' => '1c',
                ]
            ])->assertStatus(201);

        $this->assertNotNull($repository->findOneBy(['name' => '1c']));

    }

    /** @test */
    public function post_to_create_class_with_existing_name()
    {
        SchoolClassFactory::createOne(['name' => '1c']);

        $this->browser()
            ->actingAs(UserFactory::new()->asAdmin()->create())
            ->post('/api/classes', [
                'json' => [
                    'name' => '1c',
                ]
            ])->assertStatus(422);
    }

    /** @test */
    public function deletion_school_class_with_students()
    {
        $schoolClass = SchoolClassFactory::createOne();
        $studentRepository = StudentFactory::repository();

        $this->browser()
            ->actingAs(UserFactory::new()->asAdmin()->create())
            ->delete('/api/classes/' . $schoolClass->getId())
            ->assertStatus(204);

        $this->assertNull($studentRepository->findOneBy(['id' => 1])->getSchoolClass());
        $this->assertNull($studentRepository->findOneBy(['id' => 2])->getSchoolClass());
    }



}