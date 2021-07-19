<?php

namespace App\Repository;

use App\Entity\ResultCampaignUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ResultCampaignUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method ResultCampaignUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method ResultCampaignUser[]    findAll()
 * @method ResultCampaignUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResultCampaignUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ResultCampaignUser::class);
    }

    // /**
    //  * @return ResultCampaignUser[] Returns an array of ResultCampaignUser objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ResultCampaignUser
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
