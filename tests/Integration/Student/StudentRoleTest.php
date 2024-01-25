<?php

namespace App\Tests\Integration\Student;

use App\Factory\StudentFactory;
use App\Factory\UserFactory;
use App\Tests\Integration\Helper\ApiTestCase;
use Zenstruck\Browser\Json;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class StudentRoleTest extends ApiTestCase
{
    use ResetDatabase;
    use Factories;

    /** @test */
    public function get_collection_of_students()
    {
        StudentFactory::createOne();

        $this->browser()
            ->actingAs(UserFactory::new()->asTeacher()->create())
            ->get('/api/students')
            ->assertStatus(200)
            ->assertJsonMatches('"hydra:totalItems"', 1)
            ->use(function (Json $json) {
                $json->assertMatches('keys("hydra:member"[0])', [
                    '@id',
                    '@type',
                    'firstName',
                    'lastName',
                ]);
            });
    }

    /** @test */
    public function get_student_with_valid_data()
    {
        $student = StudentFactory::createOne();

        $this->browser()
            ->actingAs(UserFactory::new()->asTeacher()->create())
            ->get('/api/students/' . $student->getId())
            ->assertStatus(200)
            ->assertJsonMatches('firstName', $student->getFirstName())
            ->assertJsonMatches('lastName', $student->getLastName());
    }

    /** @test */
    public function not_existing_student()
    {
        $this->browser()
            ->actingAs(UserFactory::new()->asTeacher()->create())
            ->get('/api/students/43')
            ->assertStatus(404);
    }

    /** @test */
    public function post_with_valid_credentials_is_saving_student_to_the_database()
    {
        $user = UserFactory::createOne();
        $studentRepository = StudentFactory::repository();

        $this->browser()
            ->actingAs(UserFactory::new()->asTeacher()->create())
            ->post('/api/students', [
                'json' => [
                    'firstName' => 'John',
                    'lastName' => 'Struck',
                    'user' => '/api/users/' . $user->getId(),
                ]
            ])->assertStatus(201);

        $this->assertNotNull($studentRepository->findOneBy(['id' => $user->getStudent()->getId()]));
    }

    /** @test */
    public function post_with_valid_credentials_is_setting_student_role()
    {
        $user = UserFactory::createOne();

        $this->browser()
            ->actingAs(UserFactory::new()->asTeacher()->create())
            ->post('/api/students', [
                'json' => [
                    'firstName' => 'John',
                    'lastName' => 'Struck',
                    'user' => '/api/users/' . $user->getId(),
                ]
            ])->assertStatus(201);

        $this->assertTrue(in_array('ROLE_STUDENT', $user->getRoles()));
    }

    /** @test */
    public function post_not_existing_user()
    {
        $this->browser()
            ->actingAs(UserFactory::new()->asTeacher()->create())
            ->post('/api/students', [
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
            ->actingAs(UserFactory::new()->asTeacher()->create())
            ->post('/api/students', [
                'json' => [
                    'firstName' => 'John',
                    'lastName' => 'Struck',
                ]
            ])->assertStatus(422);
    }

    /** @test */
    public function post_when_user_was_student()
    {
        $student = StudentFactory::createOne();

        $this->browser()
            ->actingAs(UserFactory::new()->asTeacher()->create())
            ->post('/api/students', [
                'json' => [
                    'firstName' => 'John',
                    'lastName' => 'Struck',
                    'user' => '/api/users/'.$student->getUser()->getId()
                ]
            ])->assertStatus(422);
    }

    /** @test */
    public function patch_to_edit_student()
    {
        $student = StudentFactory::createOne();

        $studentId = $student->getId();

        $this->browser()
            ->actingAs(UserFactory::new()->asTeacher()->create())
            ->patch('/api/students/' . $studentId, [
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
            ->assertJsonMatches('lastName', 'Doe');

    }

    /** @test */
    public function patch_to_not_existing_student()
    {
        $this->browser()
            ->actingAs(UserFactory::new()->asTeacher()->create())
            ->patch('/api/students/43', [
                'json' => [
                    'firstName' => 'John',
                ],
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json',
                ]
            ])->assertStatus(404);

    }

    /** @test */
    public function deletion_student()
    {
        $student = StudentFactory::createOne();
        $studentRepository = StudentFactory::repository();

        $studentId = $student->getId();

        $this->browser()
            ->actingAs(UserFactory::new()->asTeacher()->create())
            ->delete('/api/students/' . $studentId)
            ->assertStatus(204);

        $this->assertNull($studentRepository->findOneBy(['id' => $studentId]));
    }

    /** @test */
    public function deletion_not_existing_student()
    {
        $this->browser()
            ->actingAs(UserFactory::new()->asTeacher()->create())
            ->delete('/api/students/949')
            ->assertStatus(404);
    }

}
