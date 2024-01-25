<?php

namespace App\Tests\Unit\Service;

use App\Message\TokenNotification;
use App\Service\TokenNotificationService;
use PHPUnit\Framework\TestCase;
use Mockery as m;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class TokenNotificationServiceTest extends TestCase
{
    private MessageBusInterface $bus;
    private TokenNotificationService $tokenNotificationService;
    private Envelope $envelope;

    protected function setUp(): void
    {
        $this->bus = m::mock(MessageBusInterface::class);
        $this->tokenNotificationService = new TokenNotificationService(
            $this->bus
        );
        $this->envelope = new Envelope($this->tokenNotificationService);
    }

    protected function tearDown(): void
    {
        m::close();
    }

    /** @test */
    public function sendTokenNotification_works_correctly()
    {
        $sendTo = 'email@example.com';
        $token = 'example-token';

        $tokenNotification = new TokenNotification($sendTo, $token);

        $this->bus
            ->shouldReceive('dispatch')
            ->once()
            ->with(m::type(TokenNotification::class))
            ->andReturn($this->envelope);

        $this->tokenNotificationService->sendTokenNotification($sendTo, $token);

        $this->assertEquals($sendTo, $tokenNotification->getSendTo());
        $this->assertEquals($token, $tokenNotification->getContent());

    }
}