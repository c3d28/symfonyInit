<?php

namespace App\Repository;

use App\Entity\Draw;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Draw|null find($id, $lockMode = null, $lockVersion = null)
 * @method Draw|null findOneBy(array $criteria, array $orderBy = null)
 * @method Draw[]    findAll()
 * @method Draw[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DrawRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Draw::class);
    }

    /**
    * @return Draw[] Returns an array of Draw objects
     */

    public function findDrawToExecute()
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.finished = false AND d.dateDraw < :date')
            ->setParameter('date', new DateTime())
            ->getQuery()
            ->getResult()
        ;
    }


    /*
    public function findOneBySomeField($value): ?Draw
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
