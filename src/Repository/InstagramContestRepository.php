<?php

namespace App\Repository;

use App\Entity\InstagramContest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method InstagramContest|null find($id, $lockMode = null, $lockVersion = null)
 * @method InstagramContest|null findOneBy(array $criteria, array $orderBy = null)
 * @method InstagramContest[]    findAll()
 * @method InstagramContest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InstagramContestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InstagramContest::class);
    }

    // /**
    //  * @return InstagramContest[] Returns an array of InstagramContest objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?InstagramContest
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
