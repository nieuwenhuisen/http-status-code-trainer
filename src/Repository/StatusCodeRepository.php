<?php

namespace App\Repository;

use App\Entity\StatusCode;
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
}
