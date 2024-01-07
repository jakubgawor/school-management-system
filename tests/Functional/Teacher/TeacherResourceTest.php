<?php

namespace App\Tests\Functional\Teacher;

use App\Factory\TeacherFactory;
use App\Factory\UserFactory;
use App\Tests\Functional\Helper\ApiTestCase;
use Zenstruck\Browser\Json;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class TeacherResourceTest extends ApiTestCase
{
    use ResetDatabase;
    use Factories;

    /** @test */
    public function get_collection_of_teachers()
    {
        TeacherFactory::createOne();

        $this->browser()
            ->actingAs(UserFactory::new()->asAdmin()->create())
            ->get('/api/teachers')
            ->assertStatus(200)
            ->assertJsonMatches('"hydra:totalItems"', 1)
            ->use(function (Json $json) {
                $json->assertMatches('keys("hydra:member"[0])', [
                    '@id',
                    '@type',
                    'firstName',
                    'lastName',
                    'user',
                ]);
            });
    }

    /** @test */
    public function get_teacher_with_valid_data()
    {
        $teacher = TeacherFactory::createOne();

        $this->browser()
            ->actingAs(UserFactory::new()->asAdmin()->create())
            ->get('/api/teachers/' . $teacher->getId())
            ->assertStatus(200)
            ->assertJsonMatches('firstName', $teacher->getFirstName())
            ->assertJsonMatches('lastName', $teacher->getLastName())
            ->assertJsonMatches('user', '/api/users/' . $teacher->getUser()->getId());
    }

    /** @test */
    public function not_existing_teacher()
    {
        $this->browser()
            ->actingAs(UserFactory::new()->asAdmin()->create())
            ->get('/api/teachers/43')
            ->assertStatus(404);
    }

    /** @test */
    public function post_is_saving_teacher_in_database()
    {
        $user = UserFactory::createOne();
        $teacherRepository = TeacherFactory::repository();

        $this->browser()
            ->actingAs(UserFactory::new()->asAdmin()->create())
            ->post('/api/teachers', [
                'json' => [
                    'firstName' => 'John',
                    'lastName' => 'Struck',
                    'user' => '/api/users/' . $user->getId(),
                ]
            ])->assertStatus(201);

        $this->assertNotNull($teacherRepository->findOneBy(['id' => $user->getTeacher()->getId()]));
    }

    /** @test */
    public function post_is_setting_teacher_role()
    {
        $user = UserFactory::createOne();

        $this->browser()
            ->actingAs(UserFactory::new()->asAdmin()->create())
            ->post('/api/teachers', [
                'json' => [
                    'firstName' => 'John',
                    'lastName' => 'Struck',
                    'user' => '/api/users/' . $user->getId(),
                ]
            ])->assertStatus(201);

        $this->assertTrue(in_array('ROLE_TEACHER', $user->getRoles()));
    }

    /** @test */
    public function deletion_teacher()
    {
        $teacher = TeacherFactory::createOne();
        $teacherRepository = TeacherFactory::repository();

        $teacherId = $teacher->getId();

        $this->browser()
            ->actingAs(UserFactory::new()->asAdmin()->create())
            ->delete('/api/teachers/' . $teacherId)
            ->assertStatus(204);

        $this->assertNull($teacherRepository->findOneBy(['id' => $teacherId]));
    }

    /** @test */
    public function deletion_not_existing_teacher()
    {
        $this->browser()
            ->actingAs(UserFactory::new()->asAdmin()->create())
            ->delete('/api/teachers/949')
            ->assertStatus(404);
    }
    
}
