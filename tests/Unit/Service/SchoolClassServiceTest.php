<?php

namespace App\Tests\Unit\Service;

use App\Entity\SchoolClass;
use App\Entity\Student;
use App\Repository\SchoolClassRepository;
use App\Service\SchoolClassService;
use ArrayIterator;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Mockery as m;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SchoolClassServiceTest extends TestCase
{
    private SchoolClassRepository $schoolClassRepository;
    private EntityManagerInterface $entityManager;
    private SchoolClassService $schoolClassService;

    protected function setUp(): void
    {
        $this->schoolClassRepository = m::mock(SchoolClassRepository::class);
        $this->entityManager = m::mock(EntityManagerInterface::class);

        $this->schoolClassService = new SchoolClassService(
            $this->schoolClassRepository,
            $this->entityManager
        );
    }

    protected function tearDown(): void
    {
        m::close();
    }

    /** @test */
    public function removeStudentsFromClass_works_correctly()
    {
        $schoolClass = m::mock(SchoolClass::class);
        $student = m::mock(Student::class);

        $studentsCollection = m::mock('Doctrine\Common\Collections\Collection');
        $studentsCollection->shouldReceive('getIterator')->andReturn(new ArrayIterator([$student]));
        $studentsCollection->shouldReceive('removeElement')->with($student);

        $schoolClass->shouldReceive('getStudents')->andReturn($studentsCollection);
        $schoolClass->shouldReceive('removeStudent')->with($student);

        $this->schoolClassService->removeStudentsFromClass($schoolClass);
        $this->assertTrue(true);

    }

    /** @test */
    public function getSchoolClassByName_finds_school_class_by_name()
    {
        $schoolClassName = '1a';
        $schoolClassMock = m::mock(SchoolClass::class);

        $this->schoolClassRepository
            ->shouldReceive('findOneBy')
            ->with(['name' => $schoolClassName])
            ->andReturn($schoolClassMock);

        $result = $this->schoolClassService->getSchoolClassByName($schoolClassName);
        $this->assertEquals($schoolClassMock, $result);
    }

    /** @test */
    public function getSchoolClassByName_throws_exception_when_not_found()
    {
        $schoolClassName = '1a';

        $this->expectException(NotFoundHttpException::class);

        $this->schoolClassRepository
            ->shouldReceive('findOneBy')
            ->with(['name' => $schoolClassName])
            ->andReturnNull();

        $this->schoolClassService->getSchoolClassByName($schoolClassName);
    }

    /** @test */
    public function addStudentToSchoolClass_works_correctly()
    {
        $schoolClass = m::mock(SchoolClass::class);
        $student = m::mock(Student::class);

        $schoolClass->shouldReceive('addStudent')->with($student);
        $this->entityManager->shouldReceive('persist')->with($schoolClass)->once();
        $this->entityManager->shouldReceive('flush')->once();

        $this->schoolClassService->addStudentToSchoolClass($schoolClass, $student);
        $this->assertTrue(true);
    }

    /** @test */
    public function removeStudentFromClass_works_correctly()
    {
        $schoolClass = m::mock(SchoolClass::class);
        $student = m::mock(Student::class);

        $schoolClass->shouldReceive('removeStudent')->with($student);
        $this->entityManager->shouldReceive('persist')->with($schoolClass)->once();
        $this->entityManager->shouldReceive('flush')->once();

        $this->schoolClassService->removeStudentFromClass($schoolClass, $student);
        $this->assertTrue(true);
    }

}