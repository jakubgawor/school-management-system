<?php

namespace App\EventSubscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\ApiResource\UserApi;
use App\Service\TokenNotificationService;
use App\Service\TokenService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class VerifyMailSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private TokenService             $tokenService,
        private TokenNotificationService $tokenNotificationService,
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

        $verificationToken = $this->tokenService->createToken($user->getId());

        $this->tokenNotificationService->sendTokenNotification($verificationToken);
    }
}