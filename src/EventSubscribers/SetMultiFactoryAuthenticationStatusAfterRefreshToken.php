<?php

declare(strict_types=1);

namespace App\EventSubscribers;

use App\Entity\JwtRefreshToken;
use App\Entity\User;
use Gesdinet\JWTRefreshTokenBundle\Event\RefreshEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class SetMultiFactoryAuthenticationStatusAfterRefreshToken implements EventSubscriberInterface
{
    private $request;

    public function __construct(RequestStack $requestStack)
    {
        $this->request = $requestStack->getCurrentRequest();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'gesdinet.refresh_token' => 'setMultiFactoryAuthenticationStatus',
        ];
    }

    public function setMultiFactoryAuthenticationStatus(RefreshEvent $event): void
    {
        if (!$this->request) {
            return;
        }

        /** @var JwtRefreshToken $refreshToken */
        $refreshToken = $event->getRefreshToken();
        $this->request->attributes->set(User::MFA_VERIFIED_KEY, $refreshToken->isMfaVerified());
    }
}
