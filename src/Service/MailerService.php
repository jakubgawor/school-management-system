<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService
{
    public function __construct(
        private MailerInterface $mailer,
    )
    {
    }

    public function sendVerificationMail(string $verificationToken): void
    {
        $message = (new Email())
            ->from('sub@example.com')
            ->to('sub@example.com')
            ->subject('Verification Token')
            ->text('Token: ' . $verificationToken);

        $this->mailer->send($message);
    }
}