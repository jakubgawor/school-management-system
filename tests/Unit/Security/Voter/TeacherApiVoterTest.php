<?php

namespace App\Tests\Unit\Security\Voter;

use App\ApiResource\TeacherApi;
use App\Entity\Teacher;
use App\Entity\User;
use App\Security\Voter\TeacherApiVoter;
use PHPUnit\Framework\TestCase;
use Mockery as m;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class TeacherApiVoterTest extends TestCase
{
    private Security $security;
    private TeacherApiVoter $teacherApiVoter;

    protected function setUp(): void
    {
        $this->security = m::mock(Security::class);

        $this->teacherApiVoter = new TeacherApiVoter($this->security);
    }

    protected function tearDown(): void
    {
        m::close();
    }

    /** @test */
    public function voteOnAttribute_returns_true_for_admin()
    {
        $token = m::mock(TokenInterface::class);
        $teacherApiMock = m::mock(TeacherApi::class);
        $userMock = m::mock(User::class);

        $token->shouldReceive('getUser')->andReturn($userMock);

        $this->security
            ->shouldReceive('isGranted')
            ->with('ROLE_ADMIN')
            ->andReturn(true);

        $result = $this->teacherApiVoter->vote($token, $teacherApiMock, [TeacherApiVoter::EDIT_TEACHER]);

        $this->assertSame(1, $result);
    }

    /** @test */
    public function voteOnAttribute_returns_true_when_id_is_equal()
    {
        $teacherId = 1;
        $user = m::mock(UserInterface::class);
        $teacher = m::mock(Teacher::class);

        $teacher->shouldReceive('getId')->andReturn($teacherId);
        $user->shouldReceive('getTeacher')->andReturn($teacher);

        $token = m::mock(TokenInterface::class);

        $teacherApi = m::mock(TeacherApi::class);
        $teacherApi->shouldReceive('getId')->andReturn($teacherId);

        $token->shouldReceive('getUser')->andReturn($user);
        $this->security->shouldReceive('isGranted')->with('ROLE_ADMIN')->andReturn(false);

        $result = $this->teacherApiVoter->vote($token, $teacherApi, [TeacherApiVoter::EDIT_TEACHER]);

        $this->assertSame(1, $result);

    }

    /** @test */
    public function voteOnAttribute_returns_false_for_different_teacher()
    {
        $teacherId = 1;
        $differentTeacherId = 2;

        $user = m::mock(UserInterface::class);

        $teacher = m::mock(Teacher::class);
        $teacher->shouldReceive('getId')->andReturn($teacherId);
        $user->shouldReceive('getTeacher')->andReturn($teacher);

        $token = m::mock(TokenInterface::class);

        $teacherApi = m::mock(TeacherApi::class);
        $teacherApi->shouldReceive('getId')->andReturn($differentTeacherId);

        $token->shouldReceive('getUser')->andReturn($user);
        $this->security->shouldReceive('isGranted')->with('ROLE_ADMIN')->andReturn(false);

        $result = $this->teacherApiVoter->vote($token, $teacherApi, [TeacherApiVoter::EDIT_TEACHER]);

        $this->assertSame(-1, $result);
    }

}