<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\JwtRefreshToken;
use App\Entity\MultiFactorAuthenticationVerify;
use App\Entity\User;
use App\Exception\MultiFactorAuthenticationCodeNotValidException;
use App\Exception\MultiFactorAuthenticationIsNotEnabledException;
use Doctrine\ORM\EntityManagerInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Sonata\GoogleAuthenticator\GoogleAuthenticator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

final class VerifyMultiFactorAuthentication
{
    private Security $security;
    private GoogleAuthenticator $googleAuthenticator;
    private JWTTokenManagerInterface $manager;
    private RequestStack $requestStack;
    private RefreshTokenManagerInterface $refreshTokenManager;
    private EntityManagerInterface $entityManager;

    public function __construct(Security $security, GoogleAuthenticator $googleAuthenticator, JWTTokenManagerInterface $manager, RequestStack $requestStack, RefreshTokenManagerInterface $refreshTokenManager, EntityManagerInterface $entityManager)
    {
        $this->security = $security;
        $this->googleAuthenticator = $googleAuthenticator;
        $this->manager = $manager;
        $this->requestStack = $requestStack;
        $this->refreshTokenManager = $refreshTokenManager;
        $this->entityManager = $entityManager;
    }

    /**
     * @throws MultiFactorAuthenticationCodeNotValidException|MultiFactorAuthenticationIsNotEnabledException
     */
    public function __invoke(MultiFactorAuthenticationVerify $data): JsonResponse
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $request = $this->requestStack->getCurrentRequest();

        if (!$user instanceof User || !$user->isMfaEnabled()) {
            throw new MultiFactorAuthenticationIsNotEnabledException('Multi Factor Authentication is not enabled.');
        }

        if (!$request || !$this->googleAuthenticator->checkCode((string) $user->getMfaKey(), $data->code)) {
            throw new MultiFactorAuthenticationCodeNotValidException('Multi Factor Authentication code is invalid.');
        }

        $request->attributes->set(User::MFA_VERIFIED_KEY, true);
        $token = $this->manager->create($user);

        /** @var JwtRefreshToken $refreshToken */
        $refreshToken = $this->refreshTokenManager->getLastFromUsername($user->getUsername());

        $refreshToken->setMfaVerified();
        $this->entityManager->flush();

        return new JsonResponse([
            'token' => $token,
            'refresh_token' => $refreshToken->getRefreshToken(),
        ]);
    }
}
