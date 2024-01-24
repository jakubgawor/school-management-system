<?php

namespace App\Service;

use App\Message\GradeNotification;
use Symfony\Component\Messenger\MessageBusInterface;

class GradeNotificationService
{
    public function __construct(
        private MessageBusInterface $bus,
    )
    {
    }

    public function sendMailNotification(string $sendTo, string $gradeValue): void
    {
        $this->bus->dispatch(new GradeNotification($sendTo, $gradeValue));
    }

}