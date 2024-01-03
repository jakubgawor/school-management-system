<?php

namespace App\EventSubscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\ApiResource\UserApi;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

final class VerifyMailSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private MailerInterface $mailer,
    )
    {
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['sendVerificationMail', EventPriorities::POST_WRITE]
        ];
    }

    public function sendVerificationMail(ViewEvent $event): void
    {
        $user = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$user instanceof UserApi || $method !== Request::METHOD_POST) {
            return;
        }

        $message = (new Email())
            ->from('sub@example.com')
            ->to('sub@example.com')
            ->subject('sub')
            ->text('sub');

        $this->mailer->send($message);


    }
}