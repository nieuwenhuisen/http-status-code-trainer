<?php

declare(strict_types=1);

namespace App\EventSubscribers;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Question;
use App\Repository\StatusCodeLogRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class QuestionAnsweredSubscriber implements EventSubscriberInterface
{
    private EntityManagerInterface $entityManager;
    private StatusCodeLogRepository $statusCodeLogRepository;

    public function __construct(EntityManagerInterface $entityManager, StatusCodeLogRepository $statusCodeLogRepository)
    {
        $this->entityManager = $entityManager;
        $this->statusCodeLogRepository = $statusCodeLogRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['postWrite', EventPriorities::POST_WRITE],
        ];
    }

    public function postWrite(ViewEvent $event): void
    {
        $object = $event->getControllerResult();

        if (!$object instanceof Question) {
            return;
        }

        $exam = $object->getExam();
        $exam->updateStatus();

        $statusCodeLog = $this->statusCodeLogRepository->findOrCreate($exam->getUser(), $object->getStatusCode());

        if ($object->isCorrect()) {
            $statusCodeLog->addCorrect();
        } else {
            $statusCodeLog->addFail();
        }

        $this->entityManager->flush();
    }
}
