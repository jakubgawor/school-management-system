<?php

namespace App\Tests\Unit\Service;

use App\Dto\StudentGradeDto;
use App\Entity\Grade;
use App\Entity\Student;
use App\Entity\Subject;
use App\Entity\Teacher;
use App\Entity\User;
use App\Repository\GradeRepository;
use App\Service\GradeService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Mockery as m;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class GradeServiceTest extends TestCase
{
    private EntityManagerInterface $entityManagerMock;
    private GradeRepository $gradeRepositoryMock;
    private GradeService $gradeService;

    public function setUp(): void
    {
        $this->entityManagerMock = m::mock(EntityManagerInterface::class);
        $this->gradeRepositoryMock = m::mock(GradeRepository::class);

        $this->gradeService = new GradeService(
            $this->entityManagerMock,
            $this->gradeRepositoryMock
        );
    }

    protected function tearDown(): void
    {
        m::close();
    }

    /** @test */
    public function verifyTeacherForSubject_if_id_is_equal_does_not_throw_exception()
    {
        $userMock = m::mock(User::class);
        $userMock->shouldReceive('getTeacher->getId')->andReturn(1);

        $subjectMock = m::mock(Subject::class);
        $subjectMock->shouldReceive('getTeacher->getId')->andReturn(1);

        $this->gradeService->verifyTeacherForSubject($userMock, $subjectMock);
        $this->assertTrue(true);

    }

    /** @test */
    public function verifyTeacherForSubject_if_id_is_not_equal_throws_exception()
    {
        $userMock = m::mock(User::class);
        $userMock->shouldReceive('getTeacher->getId')->andReturn(1);

        $subjectMock = m::mock(Subject::class);
        $subjectMock->shouldReceive('getTeacher->getId')->andReturn(2);

        $this->expectException(UnprocessableEntityHttpException::class);
        $this->gradeService->verifyTeacherForSubject($userMock, $subjectMock);

    }

    /** @test */
    public function addGrade_adds_a_grade_correctly()
    {
        $studentMock = m::mock(Student::class);
        $subjectMock = m::mock(Subject::class);
        $teacherMock = m::mock(Teacher::class);

        $this->entityManagerMock
            ->shouldReceive('persist')
            ->once()
            ->withArgs(function (Grade $grade) {
                return $grade->getGrade() === 'A' &&
                    $grade->getWeight() === 5 &&
                    $grade->getStudent() instanceof Student &&
                    $grade->getSubject() instanceof Subject &&
                    $grade->getTeacher() instanceof Teacher;
            });

        $this->entityManagerMock
            ->shouldReceive('flush')
            ->once();

        $this->gradeService->addGrade(
            'A',
            5,
            $studentMock,
            $subjectMock,
            $teacherMock
        );
        $this->assertTrue(true);

    }

    /** @test */
    public function removeGrade_removes_a_grade_correctly()
    {
        $gradeMock = m::mock(Grade::class);

        $this->entityManagerMock
            ->shouldReceive('remove')
            ->once()
            ->with($gradeMock);

        $this->entityManagerMock
            ->shouldReceive('flush')
            ->once();

        $this->gradeService->removeGrade($gradeMock);
        $this->assertTrue(true);

    }

    /** @test */
    public function averageGrade_calculates_correctly()
    {
        $grade1 = m::mock(StudentGradeDto::class);
        $grade1->grade = 3;
        $grade1->weight = 2;

        $grade2 = m::mock(StudentGradeDto::class);
        $grade2->grade = 4;
        $grade2->weight = 3;

        $average = $this->gradeService->averageGrade([$grade1, $grade2]);

        $expectedAverage = round((($grade1->grade * $grade1->weight) + ($grade2->grade * $grade2->weight)) / ($grade1->weight + $grade2->weight), 2);
        $this->assertEquals($expectedAverage, $average);
    }

    /** @test */
    public function findGradeById_finds_grade_by_id()
    {
        $gradeMock = m::mock(Grade::class);

        $this->gradeRepositoryMock
            ->shouldReceive('findOneBy')
            ->with(['id' => 1])
            ->andReturn($gradeMock);

        $grade = $this->gradeService->findGradeById(1);
        $this->assertSame($gradeMock, $grade);

    }

    /** @test */
    public function findGradeById_throws_exception_when_grade_not_found()
    {
        $this->expectException(ResourceNotFoundException::class);

        $this->gradeRepositoryMock
            ->shouldReceive('findOneBy')
            ->with(['id' => 1])
            ->andReturnNull();

        $this->gradeService->findGradeById(1);
    }

    /** @test */
    public function findGradesByStudentIdAndSubjectName_finds_grades_by_student_id_and_subject_name()
    {
        $grades = [m::mock(Grade::class), m::mock(Grade::class)];

        $this->gradeRepositoryMock
            ->shouldReceive('findGradesByStudentAndSubjectName')
            ->with(1, 'biology')
            ->andReturn($grades);

        $result = $this->gradeService->findGradesByStudentIdAndSubjectName(1, 'biology');
        $this->assertEquals($grades, $result);

    }

    /** @test */
    public function findGradesByStudentIdAndSubjectName_throws_exception_when_no_grades_found_for_student_id_and_subject_name()
    {
        $this->expectException(ResourceNotFoundException::class);

        $this->gradeRepositoryMock
            ->shouldReceive('findGradesByStudentAndSubjectName')
            ->with(1, 'biology')
            ->andReturn([]);

        $this->gradeService->findGradesByStudentIdAndSubjectName(1, 'biology');

    }

}