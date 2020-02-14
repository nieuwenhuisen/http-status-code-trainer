<?php

declare(strict_types=1);

namespace App\EventSubscribers;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class AddMultiFactoryAuthenticationPayloadSubscriber implements EventSubscriberInterface
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            Events::JWT_CREATED => 'addMultiFactoryAuthenticationPayload',
        ];
    }

    public function addMultiFactoryAuthenticationPayload(JWTCreatedEvent $event): void
    {
        $request = $this->requestStack->getCurrentRequest();

        /** @var User $user */
        $user = $event->getUser();

        if (!$request || !$user instanceof User) {
            return;
        }

        $payload = $event->getData();

        $payload[User::MFA_ENABLED_KEY] = $user->isMfaEnabled();
        $payload[User::MFA_VERIFIED_KEY] = true === $request->attributes->get(User::MFA_VERIFIED_KEY, false);

        $event->setData($payload);
    }
}
