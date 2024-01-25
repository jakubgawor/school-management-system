<?php

namespace App\Tests\Unit\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UserService;
use PHPUnit\Framework\TestCase;
use Mockery as m;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Uid\Uuid;

class UserServiceTest extends TestCase
{
    private UserRepository $userRepository;
    private UserService $userService;

    protected function setUp(): void
    {
        $this->userRepository = m::mock(UserRepository::class);
        $this->userService = new UserService($this->userRepository);
    }

    protected function tearDown(): void
    {
        m::close();
    }

    /** @test */
    public function findUserByEmail_returns_user_when_user_was_found()
    {
        $email = 'email@example.com';
        $userMock = m::mock(User::class);

        $this->userRepository
            ->shouldReceive('findOneByEmail')
            ->with($email)
            ->andReturn($userMock);

        $result = $this->userService->findUserByEmail($email);

        $this->assertInstanceOf(User::class, $result);
    }

    /** @test */
    public function findUserByEmail_returns_null_when_user_was_not_found()
    {
        $email = 'email@example.com';

        $this->userRepository
            ->shouldReceive('findOneByEmail')
            ->with($email)
            ->andReturn(null);

        $result = $this->userService->findUserByEmail($email);

        $this->assertNull($result);
    }

    /** @test */
    public function findUserByUuid_returns_user_when_user_was_found()
    {
        $uuid = Uuid::v7();
        $userMock = m::mock(User::class);

        $this->userRepository
            ->shouldReceive('findOneBy')
            ->with(['id' => $uuid])
            ->andReturn($userMock);

        $result = $this->userService->findUserByUuid($uuid);
        $this->assertEquals($userMock, $result);

    }

    /** @test */
    public function findUserByUuid_throws_exception_when_user_was_not_found()
    {
        $uuid = Uuid::v7();

        $this->expectException(NotFoundHttpException::class);

        $this->userRepository
            ->shouldReceive('findOneBy')
            ->with(['id' => $uuid])
            ->andReturnNull();

        $this->userService->findUserByUuid($uuid);
    }

}