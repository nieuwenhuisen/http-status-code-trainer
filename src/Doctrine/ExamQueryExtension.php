<?php

declare(strict_types=1);

namespace App\Doctrine;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Exam;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Security;

final class ExamQueryExtension implements QueryCollectionExtensionInterface
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * {@inheritdoc}
     */
    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null): void
    {
        if (Exam::class !== $resourceClass || $this->security->isGranted('ROLE_ADMIN') || null === $this->security->getUser()) {
            return;
        }

        $user = $this->security->getUser();

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->andWhere(sprintf('%s.user = :user', $rootAlias));
        $queryBuilder->setParameter('user', $user);
    }
}
