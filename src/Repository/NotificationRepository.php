<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Notification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DateTimeImmutable;

class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    public function save(Notification $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Notification $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getAvailableNotificationRequests(): array
    {
        return $this->createQueryBuilder('n')
            ->select('n', 'nr')
            ->leftJoin('n.receivers', 'nr')
            ->where('n.isActive = :isActive')
            ->andWhere('n.sendingDate < :now')
            ->setParameters(
                [
                    'isActive' => true,
                    'now' => new DateTimeImmutable()
                ]
            )
            ->getQuery()
            ->getResult();
    }

    public function getActiveNotificationRequests(): array
    {
        return $this->createQueryBuilder('n')
            ->select('n')
            ->where('n.isActive = :isActive')
            ->setParameter('isActive', true)
            ->getQuery()
            ->getResult();
    }

    public function getActiveNotificationWithReadings(): array
    {
        return $this->createQueryBuilder('n')
            ->select(['n', 'nr'])
            ->leftJoin('n.readings', 'nr')
            ->where('n.isActive = :isActive')
            ->setParameter('isActive', true)
            ->orderBy('nr.readAt', 'DESC')
            ->addOrderBy('n.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getAllNotificationRequestsContainsReadings(): array
    {
        return $this->createQueryBuilder('n')
            ->select('n', 'nr')
            ->leftJoin('n.readings', 'nr')
            ->where('n.isActive = :isActive')
            ->andWhere('count(nr.id) != 0')
            ->setParameter('isActive', true)
            ->getQuery()
            ->getResult();
    }
}
