<?php

namespace App\Tests\Functional\Grade;

use App\Entity\Grade;
use App\Entity\SchoolClass;
use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\User;
use App\Factory\SchoolClassFactory;
use App\Factory\SubjectFactory;
use App\Factory\TeacherFactory;
use App\Tests\Functional\Helper\ApiTestCase;
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
            ->post('/api/grade/students/'.$student->getId(), [
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


}