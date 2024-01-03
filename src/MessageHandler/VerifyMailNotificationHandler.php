<?php

namespace App\MessageHandler;

use App\Message\VerifyMailNotification;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Messenger\SendEmailMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Email;

#[AsMessageHandler]
class VerifyMailNotificationHandler
{
    public function __construct(
        private MailerInterface $mailer,
    )
    {
    }

    public function __invoke(VerifyMailNotification $verifyMailNotification)
    {
        $message = (new Email())
            ->from('sub@example.com')
            ->to('sub@example.com')
            ->subject('Verification Token')
            ->text($verifyMailNotification->getContent());

        $this->mailer->send($message);

    }
}