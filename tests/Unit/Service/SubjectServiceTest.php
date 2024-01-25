<?php

namespace App\Tests\Unit\Service;

use App\Entity\SchoolClass;
use App\Entity\Student;
use App\Entity\Subject;
use App\Entity\Teacher;
use App\Repository\SubjectRepository;
use App\Service\SubjectService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Mockery as m;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class SubjectServiceTest extends TestCase
{
    private SubjectRepository $subjectRepository;
    private EntityManagerInterface $entityManager;
    private SubjectService $subjectService;

    protected function setUp(): void
    {
        $this->subjectRepository = m::mock(SubjectRepository::class);
        $this->entityManager = m::mock(EntityManagerInterface::class);

        $this->subjectService = new SubjectService(
            $this->subjectRepository,
            $this->entityManager
        );
    }

    protected function tearDown(): void
    {
        m::close();
    }

    /** @test */
    public function validateSameNameSubject_throws_exception_when_teacher_teaches_a_subject_by_provided_name()
    {
        $name = 'biology';
        $teacherId = 1;

        $teacher = m::mock(Teacher::class);
        $teacher->shouldReceive('getId')->andReturn($teacherId);

        $subject = m::mock(Subject::class);
        $subject->shouldReceive('getTeacher->getId')->andReturn($teacherId);

        $this->subjectRepository
            ->shouldReceive('findByName')
            ->with($name)
            ->andReturn([$subject]);

        $this->expectException(UnprocessableEntityHttpException::class);
        $this->subjectService->validateSameNameSubject($name, $teacher);

    }

    /** @test */
    public function taughtSameNameSubject_throws_exception_when_school_class_has_this_subject()
    {
        $schoolClass = m::mock(SchoolClass::class);
        $subject = m::mock(Subject::class);

        $schoolClass->shouldReceive('getSubjects->getValues')->andReturn([$subject]);
        $subject->shouldReceive('getName')->andReturn('biology');

        $this->expectException(UnprocessableEntityHttpException::class);

        $this->subjectService->taughtSameNameSubject($schoolClass, $subject);
    }

    /** @test */
    public function getSubjectById_finds_subject_by_id()
    {
        $subjectId = 1;
        $subjectMock = m::mock(Subject::class);

        $this->subjectRepository
            ->shouldReceive('findOneById')
            ->with($subjectId)
            ->andReturn($subjectMock);

        $result = $this->subjectService->getSubjectById($subjectId);

        $this->assertEquals($subjectMock, $result);
    }

    /** @test */
    public function getSubjectById_throws_exception_when_subject_not_found()
    {
        $subjectId = 1;

        $this->expectException(NotFoundHttpException::class);

        $this->subjectRepository
            ->shouldReceive('findOneById')
            ->with($subjectId)
            ->andReturnNull();

        $this->subjectService->getSubjectById($subjectId);
    }

    /** @test */
    public function addSchoolClassToSubject_works_correctly()
    {
        $subject = m::mock(Subject::class);
        $schoolClass = m::mock(SchoolClass::class);

        $subject->shouldReceive('addSchoolClass')->with($schoolClass)->once();
        $this->entityManager->shouldReceive('persist')->with($subject)->once();
        $this->entityManager->shouldReceive('flush')->once();

        $this->subjectService->addSchoolClassToSubject($subject, $schoolClass);
        $this->assertTrue(true);
    }

    /** @test */
    public function removeSchoolClassFromSubject_works_correctly()
    {
        $subject = m::mock(Subject::class);
        $schoolClass = m::mock(SchoolClass::class);

        $subject->shouldReceive('removeSchoolClass')->with($schoolClass)->once();
        $this->entityManager->shouldReceive('persist')->with($subject)->once();
        $this->entityManager->shouldReceive('flush')->once();

        $this->subjectService->removeSchoolClassFromSubject($subject, $schoolClass);
        $this->assertTrue(true);

    }

    /** @test */
    public function findMatchingSubjectByNameForStudent_works_correctly()
    {
        $subjectName = 'biology';
        $student = m::mock(Student::class);
        $schoolClass = m::mock(SchoolClass::class);
        $subjectMock = m::mock(Subject::class);

        $subjectMock->shouldReceive('getId')->andReturn(1);
        $schoolClass->shouldReceive('getSubjects->getValues')->andReturn([$subjectMock]);
        $student->shouldReceive('getSchoolClass')->andReturn($schoolClass);

        $this->subjectRepository->shouldReceive('findByName')->with($subjectName)->andReturn([$subjectMock]);
        $this->subjectRepository->shouldReceive('findOneById')->with(1)->andReturn($subjectMock);

        $result = $this->subjectService->findMatchingSubjectByNameForStudent($subjectName, $student);

        $this->assertEquals($subjectMock, $result);
    }

    /** @test */
    public function findMatchingSubjectByNameForStudent_when_student_is_not_taught_provided_subject()
    {
        $subjectName = 'biology';
        $student = m::mock(Student::class);
        $schoolClass = m::mock(SchoolClass::class);
        $subjectMock = m::mock(Subject::class);

        $subjectMock->shouldReceive('getId')->andReturn(1);
        $schoolClass->shouldReceive('getSubjects->getValues')->andReturn([]);
        $student->shouldReceive('getSchoolClass')->andReturn($schoolClass);

        $this->subjectRepository->shouldReceive('findByName')->with($subjectName)->andReturn([$subjectMock]);

        $this->expectException(UnprocessableEntityHttpException::class);

        $this->subjectService->findMatchingSubjectByNameForStudent($subjectName, $student);
    }
}