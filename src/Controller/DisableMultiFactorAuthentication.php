<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Exception\MultiFactorAuthenticationIsNotEnabledException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Security;

final class DisableMultiFactorAuthentication
{
    private EntityManagerInterface $entityManager;
    private Security $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    public function __invoke(): JsonResponse
    {
        /** @var User $user */
        $user = $this->security->getUser();

        if (!$user->isMfaEnabled()) {
            throw new MultiFactorAuthenticationIsNotEnabledException('Multi Factor Authentication is not enabled.');
        }

        $user->setMfaKey(null);
        $this->entityManager->flush();

        return new JsonResponse(null, 204);
    }
}
