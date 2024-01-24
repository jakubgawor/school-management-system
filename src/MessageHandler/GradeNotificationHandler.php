<?php

namespace App\MessageHandler;

use App\Message\GradeNotification;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Email;

#[AsMessageHandler]
class GradeNotificationHandler
{
    public function __construct(
        private MailerInterface $mailer,
    )
    {
    }

    public function __invoke(GradeNotification $gradeNotification)
    {
        $message = (new Email())
            ->from('sub@example.com')
            ->to($gradeNotification->getSendTo())
            ->subject('New grade')
            ->text('New grade: ' . $gradeNotification->getGradeValue());

        $this->mailer->send($message);

    }
}