<?php

namespace App\Repository;

use App\Entity\PCMRegions;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PCMRegions|null find($id, $lockMode = null, $lockVersion = null)
 * @method PCMRegions|null findOneBy(array $criteria, array $orderBy = null)
 * @method PCMRegions[]    findAll()
 * @method PCMRegions[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PCMRegionsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PCMRegions::class);
    }

    // /**
    //  * @return PCMRegions[] Returns an array of PCMRegions objects
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
    public function findOneBySomeField($value): ?PCMRegions
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
