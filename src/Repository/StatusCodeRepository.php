<?php

namespace App\Repository;

use App\Entity\StatusCode;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method StatusCode|null find($id, $lockMode = null, $lockVersion = null)
 * @method StatusCode|null findOneBy(array $criteria, array $orderBy = null)
 * @method StatusCode[]    findAll()
 * @method StatusCode[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StatusCodeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StatusCode::class);
    }

    /**
     * @return array|StatusCode[]
     */
    public function getForUser(User $user): array
    {
        // TODO: return results based on previous user results
        return $this->createQueryBuilder('status_code')
            ->setMaxResults(20)
            ->getQuery()
            ->getResult();
    }

    public function getCodesGroupByType(): array
    {
        $codes = [];

        $results = $this->createQueryBuilder('status_code')
            ->select('status_code.id')
            ->orderBy('status_code.id')
            ->getQuery()
            ->getResult();

        foreach (array_column($results, 'id') as $code) {
            $group = (int) mb_substr($code, 0, 1) * 100;

            if (!isset($codes[$group])) {
                $codes[$group] = [];
            }

            $codes[$group][] = $code;
        }

        return $codes;
    }
}
