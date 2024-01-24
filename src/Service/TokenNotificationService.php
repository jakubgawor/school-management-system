<?php

namespace App\Service;

use App\Message\TokenNotification;
use Symfony\Component\Messenger\MessageBusInterface;

class TokenNotificationService
{
    public function __construct(
        private MessageBusInterface $bus,
    )
    {
    }

    public function sendTokenNotification(string $sendTo, string $token): void
    {
        $this->bus->dispatch(new TokenNotification($sendTo, $token));
    }
}