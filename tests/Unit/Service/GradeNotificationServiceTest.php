<?php

namespace App\Tests\Unit\Service;

use App\Message\GradeNotification;
use App\Service\GradeNotificationService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Mockery as m;

class GradeNotificationServiceTest extends TestCase
{
    private MessageBusInterface $bus;
    private GradeNotificationService $gradeNotificationService;
    private Envelope $envelope;

    protected function setUp(): void
    {
        $this->bus = m::mock(MessageBusInterface::class);
        $this->gradeNotificationService = new GradeNotificationService(
            $this->bus
        );
        $this->envelope = new Envelope($this->gradeNotificationService);
    }

    protected function tearDown(): void
    {
        m::close();
    }

    /** @test */
    public function send_mail_notification()
    {
        $sendTo = 'email@example.com';
        $gradeValue = 'B';

        $gradeNotification = new GradeNotification($sendTo, $gradeValue);

        $this->bus
            ->shouldReceive('dispatch')
            ->once()
            ->with(m::type(GradeNotification::class))
            ->andReturn($this->envelope);

        $this->gradeNotificationService->sendMailNotification($sendTo, $gradeValue);

        $this->assertEquals($sendTo, $gradeNotification->getSendTo());
        $this->assertEquals($gradeValue, $gradeNotification->getGradeValue());
    }

}