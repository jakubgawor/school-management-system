<?php

namespace App\Tests\Unit\Validator;

use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\User;
use App\Validator\UserRoleExistence;
use App\Validator\UserRoleExistenceValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class UserRoleExistenceValidatorTest extends TestCase
{
    private UserRoleExistenceValidator $validator;
    private ExecutionContextInterface $context;

    public function setUp(): void
    {
        $this->validator = new UserRoleExistenceValidator();
        $this->context = $this->createMock(ExecutionContextInterface::class);
        $this->validator->initialize($this->context);
    }

    /** @test */
    public function student_role_existence_throws_exception()
    {
        $constraint = new UserRoleExistence();

        $user = $this->createMock(User::class);

        $user->method('getStudent')->willReturn($this->createMock(Student::class));
        $user->method('getTeacher')->willReturn(null);

        $this->expectException(UnprocessableEntityHttpException::class);

        $this->validator->validate($user, $constraint);
    }

    /** @test */
    public function teacher_role_existence_throws_exception()
    {
        $constraint = new UserRoleExistence();

        $user = $this->createMock(User::class);

        $user->method('getStudent')->willReturn(null);
        $user->method('getTeacher')->willReturn($this->createMock(Teacher::class));

        $this->expectException(UnprocessableEntityHttpException::class);

        $this->validator->validate($user, $constraint);
    }

    /** @test */
    public function no_role_existence_does_not_throw_exception()
    {
        $constraint = new UserRoleExistence();

        $user = $this->createMock(User::class);

        $user->method('getStudent')->willReturn(null);
        $user->method('getTeacher')->willReturn(null);

        $this->validator->validate($user, $constraint);
        $this->assertTrue(true);
    }
}