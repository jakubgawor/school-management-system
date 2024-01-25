<?php

namespace App\Tests\Unit\Service;

use App\Entity\User;
use App\Entity\UserVerificationToken;
use App\Repository\UserVerificationTokenRepository;
use App\Service\TokenService;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Mockery as m;

class TokenServiceTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private UserVerificationTokenRepository $userVerificationTokenRepository;
    private UserService $userService;
    private TokenService $tokenService;

    protected function setUp(): void
    {
        $this->entityManager = m::mock(EntityManagerInterface::class);
        $this->userVerificationTokenRepository = m::mock(UserVerificationTokenRepository::class);
        $this->userService = m::mock(UserService::class);

        $this->tokenService = new TokenService(
            $this->entityManager,
            $this->userVerificationTokenRepository,
            $this->userService
        );
    }

    protected function tearDown(): void
    {
        m::close();
    }

    /** @test */
    public function saveToken_works_correctly()
    {
        $token = 'some-token';
        $userEntity = m::mock(User::class);

        $verificationToken = new UserVerificationToken();

        $userEntity
            ->shouldReceive('setUserVerificationToken')
            ->with(m::type(UserVerificationToken::class))
            ->once();

        $this->entityManager->shouldReceive('persist')->twice();
        $this->entityManager->shouldReceive('flush')->once();

        $this->tokenService->saveToken($token, $userEntity);
        $this->assertTrue(true);

    }

    /** @test */
    public function removeToken_works_correctly()
    {
        $token = 'token';
        $verificationToken = m::mock(UserVerificationToken::class);

        $this->userVerificationTokenRepository
            ->shouldReceive('findOneByToken')
            ->with($token)
            ->andReturn($verificationToken);

        $this->entityManager
            ->shouldReceive('remove')
            ->with($verificationToken)
            ->once();

        $this->entityManager
            ->shouldReceive('flush')
            ->once();

        $this->tokenService->removeToken($token);
        $this->assertTrue(true);
    }

    /** @test */
    public function getUserVerificationTokenEntity_returns_UserVerificationToken()
    {
        $token = 'token';
        $verificationToken = m::mock(UserVerificationToken::class);

        $this->userVerificationTokenRepository
            ->shouldReceive('findOneByToken')
            ->with($token)
            ->andReturn($verificationToken);

        $result = $this->tokenService->getUserVerificationTokenEntity($token);

        $this->assertEquals($verificationToken, $result);
    }
}