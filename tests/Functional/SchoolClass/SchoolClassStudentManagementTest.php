<?php

namespace App\Tests\Functional\SchoolClass;

use App\Factory\SchoolClassFactory;
use App\Factory\StudentFactory;
use App\Tests\Functional\Helper\ApiTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class SchoolClassStudentManagementTest extends ApiTestCase
{
    use ResetDatabase;
    use Factories;

    /** @test */
    public function add_student_to_class()
    {
        $student = StudentFactory::createOne();
        $schoolClass = SchoolClassFactory::createOne();

        $this->browser()
            ->post('/api/classes/students/add', [
                'json' => [
                    'student' => '/api/students/' . $student->getId(),
                    'name' => $schoolClass->getName(),
                ]
            ])->assertStatus(201);

        $this->assertSame(
            $schoolClass->getId(),
            $student->getSchoolClass()->getId()
        );

    }

    /** @test */
    public function add_student_not_existing_student_provided()
    {
        $schoolClass = SchoolClassFactory::createOne();

        $this->browser()
            ->post('/api/classes/students/add', [
                'json' => [
                    'student' => '/api/students/55',
                    'name' => $schoolClass->getName(),
                ]
            ])->assertStatus(404);
    }

    /** @test */
    public function add_student_not_existing_school_class_name_provided()
    {
        $student = StudentFactory::createOne();

        $this->browser()
            ->post('/api/classes/students/add', [
                'json' => [
                    'student' => '/api/students/' . $student->getId(),
                    'name' => '8e',
                ]
            ])->assertStatus(404);
    }

    /** @test */
    public function remove_student_from_the_class()
    {
        $schoolClass = SchoolClassFactory::createOne();
        $student = StudentFactory::createOne(['schoolClass' => $schoolClass]);

        $this->browser()
            ->delete('/api/classes/' . $schoolClass->getName() . '/students/' . $student->getId())
            ->assertStatus(204);

        $studentRepository = StudentFactory::repository();

        $this->assertNull(
            $studentRepository->findOneBy(['id' => $student->getId()])->getSchoolClass()
        );

    }

    /** @test */
    public function remove_student_non_matching_class()
    {
        $schoolClass = SchoolClassFactory::createOne();
        $student = StudentFactory::createOne();

        $this->browser()
            ->delete('/api/classes/' . $schoolClass->getName() . '/students/' . $student->getId())
            ->assertStatus(422);
    }

    /** @test */
    public function remove_student_not_existing_class_provided()
    {
        $student = StudentFactory::createOne();

        $this->browser()
            ->delete('/api/classes/8e/students/' . $student->getId())
            ->assertStatus(404);
    }

    /** @test */
    public function remove_student_not_existing_student_provided()
    {
        $schoolClass = SchoolClassFactory::createOne();

        $this->browser()
            ->delete('/api/classes/' . $schoolClass->getName() . '/students/99')
            ->assertStatus(404);
    }

}