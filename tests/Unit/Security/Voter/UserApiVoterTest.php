<?php

namespace App\Tests\Unit\Security\Voter;

use App\ApiResource\UserApi;
use App\Entity\User;
use App\Security\Voter\UserApiVoter;
use PHPUnit\Framework\TestCase;
use Mockery as m;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Uid\Uuid;

class UserApiVoterTest extends TestCase
{
    private Security $security;
    private UserApiVoter $userApiVoter;

    protected function setUp(): void
    {
        $this->security = m::mock(Security::class);

        $this->userApiVoter = new UserApiVoter($this->security);
    }

    protected function tearDown(): void
    {
        m::close();
    }

    /** @test */
    public function voteOnAttribute_returns_true_for_admin()
    {
        $token = m::mock(TokenInterface::class);
        $userApiMock = m::mock(UserApi::class);
        $userMock = m::mock(User::class);

        $token->shouldReceive('getUser')->andReturn($userMock);

        $this->security
            ->shouldReceive('isGranted')
            ->with('ROLE_ADMIN')
            ->andReturn(true);

        $result = $this->userApiVoter->vote($token, $userApiMock, [UserApiVoter::PATCH]);

        $this->assertSame(1, $result);
    }

    /** @test */
    public function voteOnAttribute_returns_true_when_id_is_equal()
    {
        $userId = Uuid::v7();
        $user = m::mock(User::class);

        $user->shouldReceive('getId')->andReturn($userId);

        $token = m::mock(TokenInterface::class);

        $userApi = m::mock(UserApi::class);
        $userApi->shouldReceive('getId')->andReturn($userId);

        $token->shouldReceive('getUser')->andReturn($user);
        $this->security->shouldReceive('isGranted')->with('ROLE_ADMIN')->andReturn(false);

        $result = $this->userApiVoter->vote($token, $userApi, [UserApiVoter::PATCH]);

        $this->assertSame(1, $result);

    }

    /** @test */
    public function voteOnAttribute_returns_false_when_id_is_not_equal()
    {
        $userId = Uuid::v7();
        $otherUserId = Uuid::v7();
        $user = m::mock(User::class);
        $user->shouldReceive('getId')->andReturn($userId);
        $token = m::mock(TokenInterface::class);
        $userApi = m::mock(UserApi::class);
        $userApi->shouldReceive('getId')->andReturn($otherUserId);

        $token->shouldReceive('getUser')->andReturn($user);
        $this->security->shouldReceive('isGranted')->with('ROLE_ADMIN')->andReturn(false);

        $result = $this->userApiVoter->vote($token, $userApi, [UserApiVoter::PATCH]);

        $this->assertSame(-1, $result);
    }

}