<?php declare(strict_types=1);

namespace App\EventSubscribers;

use App\Controller\VerifyMultiFactorAuthentication;
use App\Entity\User;
use App\Exception\MultiFactorAuthenticationNotVerifiedException;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTDecodedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class VerifyMultiFactorAuthenticationSubscriber implements EventSubscriberInterface
{
    private $requestStack;
    private $userRepository;

    public function __construct(RequestStack $requestStack, UserRepository $userRepository)
    {
        $this->requestStack = $requestStack;
        $this->userRepository = $userRepository;
    }
    public static function getSubscribedEvents(): array
    {
        return [
            Events::JWT_DECODED => 'verifyMultiFactorAuthentication',
        ];
    }

    public function verifyMultiFactorAuthentication(JWTDecodedEvent $event): void
    {
        $payload = $event->getPayload();
        $request = $this->requestStack->getCurrentRequest();

        if (!$request) {
            return;
        }

        // Don't check the verify API endpoint.
        if (VerifyMultiFactorAuthentication::class === $request->get('_controller')) {
            return;
        }

        // Check if Multi Factor Authentication is enabled and is verify
        if (!isset($payload[User::MFA_ENABLED_KEY]) || ($payload[User::MFA_ENABLED_KEY] && !$payload[User::MFA_VERIFIED_KEY])) {
            throw new MultiFactorAuthenticationNotVerifiedException('Multifactor Authentication not verified.');
        }

        $user = $this->userRepository->findOneBy(['email' => $payload['username']]);

        // Check if Multi Factor Authentication is enabled after login
        if ($user && !$payload[User::MFA_ENABLED_KEY] && $user->isMfaEnabled()) {
            $event->markAsInvalid();
        }
    }
}
