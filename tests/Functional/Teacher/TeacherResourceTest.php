<?php

namespace App\Tests\Functional\Teacher;

use App\Factory\StudentFactory;
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
    public function post_with_valid_credentials_is_saving_teacher_to_the_database()
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
    public function post_with_user_field_set_to_user_with_student_role()
    {
        $this->browser()
            ->actingAs(UserFactory::new()->asAdmin()->create())
            ->post('/api/teachers', [
                'json' => [
                    'firstName' => 'John',
                    'lastName' => 'Struck',
                    'user' => '/api/users/' . StudentFactory::createOne()->getUser()->getId(),
                ]
            ])->assertStatus(422);
    }

    /** @test */
    public function post_with_valid_credentials_is_setting_teacher_role()
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
    public function post_not_existing_user()
    {
        $this->browser()
            ->actingAs(UserFactory::new()->asAdmin()->create())
            ->post('/api/teachers', [
                'json' => [
                    'firstName' => 'John',
                    'lastName' => 'Struck',
                    'user' => '/api/users/fb631912-4e87-4bbc-acfe-b093b35119a7',
                ]
            ])->assertStatus(404);
    }

    /** @test */
    public function post_without_providing_user()
    {
        $this->browser()
            ->actingAs(UserFactory::new()->asAdmin()->create())
            ->post('/api/teachers', [
                'json' => [
                    'firstName' => 'John',
                    'lastName' => 'Struck',
                ]
            ])->assertStatus(422);
    }

    /** @test */
    public function post_when_user_was_teacher()
    {
        $teacher = TeacherFactory::createOne();

        $this->browser()
            ->actingAs(UserFactory::new()->asAdmin()->create())
            ->post('/api/teachers', [
                'json' => [
                    'firstName' => 'John',
                    'lastName' => 'Struck',
                    'user' => '/api/users/' . $teacher->getUser()->getId()
                ]
            ])->assertStatus(422);
    }

    /** @test */
    public function patch_to_edit_teacher()
    {
        $teacher = TeacherFactory::createOne();

        $teacherId = $teacher->getId();

        $this->browser()
            ->actingAs(UserFactory::new()->asAdmin()->create())
            ->patch('/api/teachers/' . $teacherId, [
                'json' => [
                    'firstName' => 'John',
                    'lastName' => 'Doe',
                ],
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json',
                ]
            ])
            ->assertStatus(200)
            ->assertJsonMatches('firstName', 'John')
            ->assertJsonMatches('lastName', 'Doe')
            ->assertJsonMatches('user', '/api/users/' . $teacher->getUser()->getId());

    }

    /** @test */
    public function patch_user_field_to_override_teacher()
    {
        $teacher = TeacherFactory::createOne();
        $teacher2 = TeacherFactory::createOne();
        $teacherId = $teacher->getId();

        $this->browser()
            ->actingAs(UserFactory::new()->asAdmin()->create())
            ->patch('/api/teachers/' . $teacherId, [
                'json' => [
                    'firstName' => 'John',
                    'lastName' => 'Doe',
                    'user' => '/api/users/' . $teacher2->getUser()->getId(),
                ],
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json',
                ]
            ])->assertStatus(409);

    }

    /** @test */
    public function patch_user_field_to_override_student_role()
    {
        $teacher = TeacherFactory::createOne();
        $student = StudentFactory::createOne();
        $teacherId = $teacher->getId();

        $this->browser()
            ->actingAs(UserFactory::new()->asAdmin()->create())
            ->patch('/api/teachers/' . $teacherId, [
                'json' => [
                    'firstName' => 'John',
                    'lastName' => 'Doe',
                    'user' => '/api/users/' . $student->getUser()->getId(),
                ],
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json',
                ]
            ])->assertStatus(409);

    }

    /** @test */
    public function patch_not_existing_user_provided()
    {
        $teacher = TeacherFactory::createOne();

        $this->browser()
            ->actingAs(UserFactory::new()->asAdmin()->create())
            ->patch('/api/teachers/' . $teacher->getId(), [
                'json' => [
                    'firstName' => 'John',
                    'lastName' => 'Doe',
                    'user' => '/api/users/fb631912-4e87-4bbc-acfe-b093b35119a7',
                ],
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json',
                ]
            ])->assertStatus(404);

    }

    /** @test */
    public function patch_to_not_existing_teacher()
    {
        $this->browser()
            ->actingAs(UserFactory::new()->asAdmin()->create())
            ->patch('/api/teachers/43', [
                'json' => [
                    'firstName' => 'John',
                ],
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json',
                ]
            ])->assertStatus(404);

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
