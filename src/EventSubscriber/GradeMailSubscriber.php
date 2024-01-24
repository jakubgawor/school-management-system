<?php

namespace App\EventSubscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Dto\AddGradeDto;
use App\Service\GradeNotificationService;
use App\Service\StudentService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class GradeMailSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private GradeNotificationService $gradeNotificationService,
        private StudentService        $studentService,
    )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['sendGradeNotificationMail', EventPriorities::POST_WRITE],
        ];
    }

    public function sendGradeNotificationMail(ViewEvent $event): void
    {
        $grade = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$grade instanceof AddGradeDto || Request::METHOD_POST !== $method) {
            return;
        }

        $studentId = $event->getRequest()->get('_route_params')['studentId'];
        $student = $this->studentService->getStudentById($studentId);

        $this->gradeNotificationService->sendMailNotification($student->getUser()->getEmail(), $grade->grade);

    }
}
