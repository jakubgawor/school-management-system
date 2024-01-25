<?php

namespace App\Tests\Functional\Subject;

use App\Factory\SchoolClassFactory;
use App\Factory\StudentFactory;
use App\Factory\SubjectFactory;
use App\Factory\TeacherFactory;
use App\Tests\Functional\Helper\ApiTestCase;
use Zenstruck\Browser\Json;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class SubjectApiTest extends ApiTestCase
{
    use ResetDatabase;
    use Factories;

    /** @test */
    public function get_collection_of_subjects()
    {
        SubjectFactory::createOne();

        $this->browser()
            ->get('/api/subjects')
            ->assertStatus(200)
            ->use(function (Json $json) {
                $json->assertMatches('keys("hydra:member"[0])', [
                    '@id',
                    '@type',
                    'name',
                    'schoolClasses',
                    'teacher',
                ]);
            });
    }

    /** @test */
    public function trying_to_retrieve_not_existing_subject()
    {
        $this->browser()
            ->get('/api/subjects/40')
            ->assertStatus(404);
    }

    /** @test */
    public function post_to_create_subject()
    {
        $teacher = TeacherFactory::createOne();

        $this->browser()
            ->post('/api/subjects', [
                'json' => [
                    'name' => 'biology',
                    'teacher' => '/api/teachers/' . $teacher->getId()
                ]
            ])->assertStatus(201);
    }

    /** @test */
    public function post_to_create_subject_without_teacher()
    {
        $this->browser()
            ->post('/api/subjects', [
                'json' => [
                    'name' => 'biology',
                ]
            ])->assertStatus(422);
    }

    /** @test */
    public function post_to_create_subject_without_name()
    {
        $this->browser()
            ->post('/api/subjects', [
                'json' => [
                    'teacher' => '/api/teachers/' . TeacherFactory::createOne()->getId(),
                ]
            ])->assertStatus(422);
    }

    /** @test */
    public function post_to_create_new_subject_with_not_existing_teacher()
    {
        $this->browser()
            ->post('/api/subjects', [
                'json' => [
                    'name' => '',
                    'teacher' => '/api/teachers/43',
                ]
            ])->assertStatus(404);
    }

    /** @test */
    public function post_to_create_new_subject_same_name_provided()
    {
        $teacher = TeacherFactory::createOne();
        $subject = SubjectFactory::createOne(['teacher' => $teacher]);

        $this->browser()
            ->post('/api/subjects', [
                'json' => [
                    'name' => $subject->getName(),
                    'teacher' => '/api/teachers/' . $teacher->getId(),
                ]
            ])->assertStatus(422);

    }

    /** @test */
    public function post_to_create_subject_with_existing_name_belongs_to_other_teacher()
    {
        SubjectFactory::createOne(['name' => 'biology', 'teacher' => TeacherFactory::createOne()]);
        $teacher = TeacherFactory::createOne();

        $this->browser()
            ->post('/api/subjects', [
                'json' => [
                    'name' => 'biology',
                    'teacher' => '/api/teachers/' . $teacher->getId(),
                ]
            ])->assertStatus(201);
    }

    /** @test */
    public function delete_subject()
    {
        $subject = SubjectFactory::createOne();

        $this->browser()
            ->delete('/api/subjects/' . $subject->getId())
            ->assertStatus(204);
    }

    /** @test */
    public function patch_to_update_subject()
    {
        $subject = SubjectFactory::createOne();
        $teacher = TeacherFactory::createOne();

        $this->browser()
            ->patch('/api/subjects/' . $subject->getId(), [
                'json' => [
                    'name' => 'chemistry',
                    'teacher' => '/api/teachers/' . $teacher->getId(),
                ],
                'headers' => ['Content-Type' => 'application/merge-patch+json']
            ])
            ->assertStatus(200);

        $this->assertSame($subject->getTeacher()->getId(), $teacher->getId());
    }

    /** @test */
    public function patch_to_update_subject_same_name_provided()
    {
        $teacher = TeacherFactory::createOne();
        $subject1 = SubjectFactory::createOne(['teacher' => $teacher]);
        $subject2 = SubjectFactory::createOne(['teacher' => $teacher]);

        $this->browser()
            ->patch('/api/subjects/' . $subject1->getId(), [
                'json' => [
                    'name' => $subject2->getName(),
                ],
                'headers' => ['Content-Type' => 'application/merge-patch+json']
            ])->assertStatus(422);
    }

    /** @test */
    public function patch_to_update_teacher()
    {
        $subject = SubjectFactory::createOne();
        $teacher = TeacherFactory::createOne();

        $this->browser()
            ->patch('/api/subjects/' . $subject->getId(), [
                'json' => [
                    'teacher' => '/api/teachers/' . $teacher->getId(),
                ],
                'headers' => ['Content-Type' => 'application/merge-patch+json'],
            ])->assertStatus(200);

        $this->assertSame($subject->getTeacher()->getId(), $teacher->getId());

    }


    /** @test */
    public function post_to_add_school_class()
    {
        $subject = SubjectFactory::createOne();
        $schoolClass = SchoolClassFactory::createOne();

        $this->browser()
            ->post('/api/subjects/classes/add', [
                'json' => [
                    'subjectId' => $subject->getId(),
                    'schoolClassName' => $schoolClass->getName(),
                ]
            ])->assertStatus(201);

    }

    /** @test */
    public function post_to_add_school_class_to_existing_subject_name_in_this_school_class()
    {
        $subject1 = SubjectFactory::createOne(['name' => 'biology'])->object();
        $subject2 = SubjectFactory::createOne(['name' => 'biology'])->object();

        $student = StudentFactory::createOne()->object();

        $schoolClass = SchoolClassFactory::createOne(['name' => '5w']);
        $schoolClass->addStudent($student);
        $schoolClass->addSubject($subject1);
        $schoolClass->save();

        $this->browser()
            ->post('/api/subjects/classes/add', [
                'json' => [
                    'subjectId' => $subject2->getId(),
                    'schoolClassName' => $schoolClass->getName(),
                ]
            ])->assertStatus(422);

    }

    /** @test */
    public function post_to_add_school_class_with_not_existing_school_class()
    {
        $subject = SubjectFactory::createOne();

        $this->browser()
            ->post('/api/subjects/classes/add', [
                'json' => [
                    'subjectId' => $subject->getId(),
                    'schoolClassName' => '7zz',
                ]
            ])->assertStatus(404);
    }

    /** @test */
    public function post_to_add_school_class_not_existing_subject()
    {
        $schoolClass = SchoolClassFactory::createOne();

        $this->browser()
            ->post('/api/subjects/classes/add', [
                'json' => [
                    'subjectId' => 43,
                    'schoolClassName' => $schoolClass->getName(),
                ]
            ])->assertStatus(404);
    }

    /** @test */
    public function post_to_add_school_class_with_blank_data()
    {
        $this->browser()
            ->post('/api/subjects/classes/add', [
                'json' => [
                ]
            ])->assertStatus(422);
    }

    /** @test */
    public function remove_school_class_from_subject()
    {
        $subject = SubjectFactory::createOne();
        $schoolClassName = $subject->getSchoolClasses()->getValues()[0]->getName();

        $this->browser()
            ->delete('/api/subjects/' . $subject->getId() . '/classes/' . $schoolClassName)
            ->assertStatus(204);

    }

}