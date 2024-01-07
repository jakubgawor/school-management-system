<?php

namespace App\Tests\Functional\Student;

use App\Factory\StudentFactory;
use App\Factory\UserFactory;
use App\Tests\Functional\Helper\ApiTestCase;
use Zenstruck\Browser\Json;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class StudentResourceTest extends ApiTestCase
{
    use ResetDatabase;
    use Factories;

    /** @test */
    public function get_collection_of_students()
    {
        StudentFactory::createOne();

        $this->browser()
            ->get('/api/students')
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
    public function get_student_with_valid_data()
    {
        $student = StudentFactory::createOne();

        $this->browser()
            ->get('/api/students/' . $student->getId())
            ->assertStatus(200)
            ->assertJsonMatches('firstName', $student->getFirstName())
            ->assertJsonMatches('lastName', $student->getLastName())
            ->assertJsonMatches('user', '/api/users/' . $student->getUser()->getId());
    }

    /** @test */
    public function not_existing_student()
    {
        $this->browser()
            ->get('/api/students/43')
            ->assertStatus(404);
    }

    /** @test */
    public function post_is_saving_student_in_database()
    {
        $user = UserFactory::createOne();
        $studentRepository = StudentFactory::repository();

        $this->browser()
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
    public function deletion_student()
    {
        $student = StudentFactory::createOne();
        $studentRepository = StudentFactory::repository();

        $studentId = $student->getId();

        $this->browser()
            ->delete('/api/students/' . $studentId)
            ->assertStatus(204);

        $this->assertNull($studentRepository->findOneBy(['id' => $studentId]));
    }

    /** @test */
    public function deletion_not_existing_student()
    {
        $this->browser()
            ->delete('/api/students/949')
            ->assertStatus(404);
    }
    
}