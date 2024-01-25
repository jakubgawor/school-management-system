<?php

namespace App\Tests\Unit\Service;

use App\Entity\Student;
use App\Repository\StudentRepository;
use App\Service\StudentService;
use PHPUnit\Framework\TestCase;
use Mockery as m;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class StudentServiceTest extends TestCase
{
    private StudentRepository $studentRepository;
    private StudentService $studentService;

    protected function setUp(): void
    {
        $this->studentRepository = m::mock(StudentRepository::class);
        $this->studentService = new StudentService($this->studentRepository);
    }

    protected function tearDown(): void
    {
        m::close();
    }

    /** @test */
    public function getStudentById_finds_student_by_id()
    {
        $studentId = 1;
        $studentMock = m::mock(Student::class);

        $this->studentRepository
            ->shouldReceive('findOneBy')
            ->with(['id' => $studentId])
            ->andReturn($studentMock);

        $result = $this->studentService->getStudentById($studentId);
        $this->assertEquals($studentMock, $result);
    }

    /** @test */
    public function getStudentById_throws_exception_when_student_not_found()
    {
        $studentId = 1;

        $this->expectException(NotFoundHttpException::class);

        $this->studentRepository
            ->shouldReceive('findOneBy')
            ->with(['id' => $studentId])
            ->andReturnNull();

        $this->studentService->getStudentById($studentId);
    }
}