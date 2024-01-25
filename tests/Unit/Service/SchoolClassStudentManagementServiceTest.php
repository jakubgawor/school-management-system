<?php

namespace App\Tests\Unit\Service;

use App\Entity\SchoolClass;
use App\Entity\Student;
use App\Service\SchoolClassStudentManagementService;
use PHPUnit\Framework\TestCase;
use Mockery as m;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class SchoolClassStudentManagementServiceTest extends TestCase
{
    private SchoolClassStudentManagementService $schoolClassStudentManagementService;

    protected function setUp(): void
    {
        $this->schoolClassStudentManagementService = new SchoolClassStudentManagementService();
    }

    protected function tearDown(): void
    {
        m::close();
    }

    /** @test */
    public function validateStudentClass_when_user_belongs_to_class()
    {
        $studentMock = m::mock(Student::class);
        $schoolClassMock = m::mock(SchoolClass::class);

        $studentMock->shouldReceive('getSchoolClass')->andReturn($schoolClassMock);

        $this->schoolClassStudentManagementService->validateStudentClass($studentMock, $schoolClassMock);
        $this->assertTrue(true);
    }

    /** @test */
    public function validateStudentClass_throws_exception_when_student_does_not_belong_to_class()
    {
        $studentMock = m::mock(Student::class);
        $schoolClassMock = m::mock(SchoolClass::class);
        $differentSchoolClassMock = m::mock(SchoolClass::class);

        $studentMock->shouldReceive('getSchoolClass')->andReturn($differentSchoolClassMock);

        $this->expectException(UnprocessableEntityHttpException::class);
        $this->schoolClassStudentManagementService->validateStudentClass($studentMock, $schoolClassMock);
        $this->assertTrue(true);
    }

}