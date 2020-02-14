<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Exception\MultiFactorAuthenticationIsAlreadyEnabledException;
use Doctrine\ORM\EntityManagerInterface;
use Sonata\GoogleAuthenticator\GoogleAuthenticator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Security;

final class EnableMultiFactorAuthentication
{
    private $entityManager;
    private $googleAuthenticator;
    private $security;

    public function __construct(EntityManagerInterface $entityManager, GoogleAuthenticator $googleAuthenticator, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->googleAuthenticator = $googleAuthenticator;
        $this->security = $security;
    }

    public function __invoke()
    {
        /** @var User $user */
        $user = $this->security->getUser();

        if ($user->isMfaEnabled()) {
            throw new MultiFactorAuthenticationIsAlreadyEnabledException('Multi Factor authentication is already enabled.');
        }

        // Generate a secret key and store it in the user entity.
        $mfaKey = $this->googleAuthenticator->generateSecret();
        $user->setMfaKey($mfaKey);

        $this->entityManager->flush();

        return new JsonResponse([
            'secret' => $mfaKey,
        ]);
    }
}
