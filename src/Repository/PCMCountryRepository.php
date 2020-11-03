<?php

namespace App\Repository;

use App\Entity\PCMCountry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PCMCountry|null find($id, $lockMode = null, $lockVersion = null)
 * @method PCMCountry|null findOneBy(array $criteria, array $orderBy = null)
 * @method PCMCountry[]    findAll()
 * @method PCMCountry[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PCMCountryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PCMCountry::class);
    }

    // /**
    //  * @return PCMCountry[] Returns an array of PCMCountry objects
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
    public function findOneBySomeField($value): ?PCMCountry
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
