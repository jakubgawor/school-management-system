<?php

namespace App\Tests\Unit\Security\Voter;

use App\Dto\GradeAverageDto;
use App\Entity\User;
use App\Security\Voter\GradeAverageVoter;
use PHPUnit\Framework\TestCase;
use Mockery as m;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class GradeAverageVoterTest extends TestCase
{
    private Security $security;
    private GradeAverageVoter $voter;

    protected function setUp(): void
    {
        $this->security = m::mock(Security::class);

        $this->voter = new GradeAverageVoter($this->security);
    }

    protected function tearDown(): void
    {
        m::close();
    }

    /** @test */
    public function voteOnAttribute_returns_true_for_teacher()
    {
        $token = m::mock(TokenInterface::class);
        $gradeAverageDtoMock = m::mock(GradeAverageDto::class);
        $userMock = m::mock(User::class);

        $token->shouldReceive('getUser')->andReturn($userMock);

        $this->security
            ->shouldReceive('isGranted')
            ->with('ROLE_TEACHER')
            ->andReturn(true);

        $result = $this->voter->vote($token, $gradeAverageDtoMock, [GradeAverageVoter::VIEW_AVERAGE]);

        $this->assertSame(1, $result);
    }

    /** @test */
    public function voteOnAttribute_returns_true_for_equal_id()
    {
        $student = m::mock(UserInterface::class);
        $student->shouldReceive('getStudent->getId')->andReturn(123);
        $token = m::mock(TokenInterface::class);
        $token->shouldReceive('getUser')->andReturn($student);
        $this->security->shouldReceive('isGranted')->with('ROLE_TEACHER')->andReturn(false);

        $gradeAverageDto = new GradeAverageDto();
        $gradeAverageDto->studentId = 123;

        $result = $this->voter->vote($token, $gradeAverageDto, [GradeAverageVoter::VIEW_AVERAGE]);

        $this->assertSame(1, $result);
    }

    /** @test */
    public function voteOnAttribute_returns_false_when_is_is_not_equal()
    {
        $student = m::mock(UserInterface::class);
        $student->shouldReceive('getStudent->getId')->andReturn(123);
        $token = m::mock(TokenInterface::class);
        $token->shouldReceive('getUser')->andReturn($student);
        $this->security->shouldReceive('isGranted')->with('ROLE_TEACHER')->andReturn(false);

        $gradeAverageDto = new GradeAverageDto();
        $gradeAverageDto->studentId = 999;

        $result = $this->voter->vote($token, $gradeAverageDto, [GradeAverageVoter::VIEW_AVERAGE]);

        $this->assertSame(-1, $result);

    }
}