<?php

namespace App\Tests\Functional\Grade;

use App\Entity\Grade;
use App\Entity\SchoolClass;
use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\User;
use App\Factory\GradeFactory;
use App\Factory\SchoolClassFactory;
use App\Factory\SubjectFactory;
use App\Factory\TeacherFactory;
use App\Tests\Functional\Helper\ApiTestCase;
use Zenstruck\Browser\Json;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;
use function Zenstruck\Foundry\repository;

class GradeApiTest extends ApiTestCase
{
    use Factories;
    use ResetDatabase;

    /** @test */
    public function post_to_add_grade_to_student()
    {
        /** @var SchoolClass $schoolClass */
        $schoolClass = SchoolClassFactory::new(['name' => '9z'])->withStudents(1)->create()->object();

        /** @var Student $student */
        $student = $schoolClass->getStudents()->getValues()[0];

        /** @var Teacher $teacher */
        $teacher = TeacherFactory::createOne();

        /** @var User $user */
        $user = $teacher->getUser();
        $user->setRoles(['ROLE_TEACHER']);

        SubjectFactory::createOne(['name' => 'biology']);
        $subject = SubjectFactory::createOne(['name' => 'biology', 'teacher' => $teacher]);
        $subject->addSchoolClass($schoolClass);
        $subject->save();

        $this->browser()
            ->actingAs($user)
            ->post('/api/grade/students/' . $student->getId(), [
                'json' => [
                    'subject' => $subject->getName(),
                    'grade' => 'B',
                    'weight' => 2,
                ]
            ])->assertStatus(201);

        $gradeRepository = repository(Grade::class);
        $this->assertNotNull($gradeRepository->findOneBy([
            'grade' => '5.00',
            'weight' => 2,
            'subject' => $subject,
            'teacher' => $user->getTeacher(),
            'student' => $student,
        ]));

    }

    /** @test */
    public function get_student_grades_of_specific_subject()
    {
        $schoolClass = SchoolClassFactory::new(['name' => '9z'])->withStudents(1)->create()->object();
        $student = $schoolClass->getStudents()->getValues()[0];
        $teacher = TeacherFactory::createOne();
        $subject = SubjectFactory::createOne(['name' => 'biology', 'teacher' => $teacher]);
        $subject->addSchoolClass($schoolClass);
        $subject->save();


        GradeFactory::createMany(2, [
            'grade' => '5.00',
            'student' => $student,
            'subject' => $subject,
            'teacher' => $teacher,
        ]);

        $this->browser()
            ->get('/api/grades/students/' . $student->getId() . '/' . $subject->getName())
            ->assertStatus(200)
            ->use(function (Json $json) {
                $json->assertMatches('keys("hydra:member"[0])', [
                    '@type',
                    '@id',
                    'id',
                    'grade',
                    'weight',
                    'issuedBy',
                ]);
            });
    }

    /** @test */
    public function delete_grade()
    {
        $schoolClass = SchoolClassFactory::new(['name' => '9z'])->withStudents(1)->create()->object();
        $student = $schoolClass->getStudents()->getValues()[0];
        $teacher = TeacherFactory::createOne();
        $subject = SubjectFactory::createOne(['name' => 'biology', 'teacher' => $teacher]);
        $subject->addSchoolClass($schoolClass);
        $subject->save();


        $grades = GradeFactory::createMany(1, [
            'grade' => '5.00',
            'student' => $student,
            'subject' => $subject,
            'teacher' => $teacher,
        ]);
        $gradeId = $grades[0]->getId();
        $this->assertSame(1, $gradeId);

        $this->browser()
            ->delete('/api/grade/' . $gradeId)
            ->assertStatus(204);


        $gradeRepository = repository(Grade::class);
        $this->assertNull($gradeRepository->findOneBy(['id' => $gradeId]));

    }

    /** @test */
    public function get_student_average()
    {
        $schoolClass = SchoolClassFactory::new(['name' => '9z'])->withStudents(1)->create()->object();
        $student = $schoolClass->getStudents()->getValues()[0];
        $teacher = TeacherFactory::createOne();
        $subject = SubjectFactory::createOne(['name' => 'biology', 'teacher' => $teacher]);
        $subject->addSchoolClass($schoolClass);
        $subject->save();

        GradeFactory::createMany(2, [
            'grade' => '4.00',
            'student' => $student,
            'subject' => $subject,
            'teacher' => $teacher,
            'weight' => 1
        ]);

        GradeFactory::createMany(2, [
            'grade' => '2.50',
            'student' => $student,
            'subject' => $subject,
            'teacher' => $teacher,
            'weight' => 3
        ]);

        GradeFactory::createMany(2, [
            'grade' => '5.00',
            'student' => $student,
            'subject' => $subject,
            'teacher' => $teacher,
        ]);

        // average = 3.58

        $this->browser()
            ->get('/api/grades/student/' . $student->getId() . '/' . $subject->getName() .'/average')
            ->assertStatus(200)
            ->assertJsonMatches('average', 3.58);

    }


}