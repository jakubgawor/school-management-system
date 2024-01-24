<?php

namespace App\MessageHandler;

use App\Message\TokenNotification;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Email;

#[AsMessageHandler]
class TokenNotificationHandler
{
    public function __construct(
        private MailerInterface $mailer,
    )
    {
    }

    public function __invoke(TokenNotification $verifyMailNotification)
    {
        $message = (new Email())
            ->from('sub@example.com')
            ->to($verifyMailNotification->getSendTo())
            ->subject('Verification Token')
            ->text($verifyMailNotification->getContent());

        $this->mailer->send($message);

    }
}