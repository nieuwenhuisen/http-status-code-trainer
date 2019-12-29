<?php

namespace App\Repository;

use App\Entity\StatusCode;
use App\Entity\StatusCodeLog;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method StatusCodeLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method StatusCodeLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method StatusCodeLog[]    findAll()
 * @method StatusCodeLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StatusCodeLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StatusCodeLog::class);
    }

    public function findOrCreate(User $user, StatusCode $statusCode): StatusCodeLog
    {
        $statusCodeLog = $this->findOneBy([
           'user' => $user,
           'statusCode' => $statusCode,
        ]);

        if (!$statusCodeLog) {
            $statusCodeLog = new StatusCodeLog($user, $statusCode);
            $this->getEntityManager()->persist($statusCodeLog);
        }

        return $statusCodeLog;
    }
}
