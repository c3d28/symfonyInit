<?php

namespace App\Repository;

use App\Entity\PcmCyclists;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method PcmCyclists|null find($id, $lockMode = null, $lockVersion = null)
 * @method PcmCyclists|null findOneBy(array $criteria, array $orderBy = null)
 * @method PcmCyclists[]    findAll()
 * @method PcmCyclists[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PcmCyclistsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PcmCyclists::class);
    }

    // /**
    //  * @return PcmCyclists[] Returns an array of PcmCyclists objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PcmCyclists
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
